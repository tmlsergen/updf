<?php

namespace App\Helpers;

use Storage;
use App;
use Log;

use App\Models\Cloud;

class ProcessCloud
{
    protected $cloud;
    protected $pdfPath, $svgPath, $pngPath, $jpgPath, $sPngPath, $sJpgPath, $htmlPath;

    public function __construct(Cloud $cloud)
    {
        $this->cloud = $cloud;
    }

    public function makeProcess()
    {

        $this->html();
        $this->pdf();
        //$this->png();
        //$this->jpg();
        $this->svg();

        $this->cloud->update();

        return [
            'code' => $this->cloud->code,
            //'sjpg' => $this->cloud->sjpg
        ];
    }

    public function html()
    {
        $temp = uniqid() . ".html";
        //put html to local to convert pdf
        Storage::put('updf/'.$temp, $this->cloud->content);
        $this->htmlPath = Storage::path('updf/'.$temp);
        $this->pdfPath = str_replace('.html', '.pdf', $this->htmlPath);
        $this->svgPath = str_replace('.html', '.svg', $this->htmlPath);
        $this->pngPath = str_replace('.html', '.png', $this->htmlPath);
        $this->jpgPath = str_replace('.html', '.jpg', $this->htmlPath);
        $this->sPngPath = str_replace('.html', '-standard.png', $this->htmlPath);//standard png
        $this->sJpgPath = str_replace('.html', '-standard.jpg', $this->htmlPath);//standard jpg

        //put html to s3
        $this->cloud->html = Storage::cloud()->putFile('updf/html', $this->htmlPath);
        Log::info("Html done");

    }

    public function pdf()
    {
        $chrome = exec("which google-chrome");

        if (App::isLocal())
            $chrome = '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome';

        if (!$chrome)
            throw new \Exception ('Library missing (1)');

        //convert html to a3 pdf (a3 is in css)
        $cmd = $chrome . " --no-sandbox --headless --print-to-pdf=" . $this->pdfPath . " " . $this->htmlPath;
        Log::info($cmd);
        $result = exec($cmd);

        //crop whitespaces
        //https://pypi.org/project/pdfCropMargins/
        $pdfcropmargins = exec("which pdf-crop-margins");
        if ($pdfcropmargins) {
            $croppedPath = str_replace('.pdf', '_cropped.pdf', $this->pdfPath);
            $cmd = $pdfcropmargins . " -p 2 " . $this->pdfPath . " -o " . $croppedPath;
            Log::info($cmd);
            $result = exec($cmd);
            $this->pdfPath = $croppedPath;
        }

        $this->cloud->pdf = Storage::cloud()->putFile('updf/pdf', $this->pdfPath);
        Log::info("Pdf done");
    }

    public function png()
    {
        //convert pdf to png //brew install poppler
        $pdftoppm = exec("which pdftoppm");
        if (!$pdftoppm)
            throw new \Exception ('Library missing (2)');

        $cmd = $pdftoppm . " " . $this->pdfPath . "  -scale-to " . config('site.hq_png') . " -png > " . $this->pngPath;
        Log::info($cmd);
        $result = exec($cmd);
        $this->cloud->png = Storage::cloud()->putFile('updf/png', $this->pngPath);

        //low resolution
        $cmd = $pdftoppm . " " . $this->pdfPath . " -scale-to " . config('site.standard_png') . " -png > " . $this->sPngPath;
        Log::info($cmd);
        $result = exec($cmd);
        $this->cloud->spng = Storage::cloud()->putFile('updf/spng', $this->sPngPath, 'public');
        Log::info("Png done");
    }

    public function jpg()
    {
        //convert pdf to jpg
        $pdftoppm = exec("which pdftoppm");
        if (!$pdftoppm)
            throw new \Exception ('Library missing (3)');

        $cmd = $pdftoppm . " " . $this->pdfPath . " -scale-to " . config('site.hq_jpg') . " -jpeg -jpegopt quality=100 > " . $this->jpgPath;
        Log::info($cmd);
        $result = exec($cmd);
        $this->cloud->jpg = Storage::cloud()->putFile('updf/jpg', $this->jpgPath);

        //low resolution
        $cmd = $pdftoppm . " " . $this->pdfPath . " -scale-to " . config('site.standard_jpg') . "  -jpeg -jpegopt quality=90 > " . $this->sJpgPath;
        Log::info($cmd);
        $result = exec($cmd);
        $this->cloud->sjpg = Storage::cloud()->putFile('updf/sjpg', $this->sJpgPath, 'public');
        Log::info("Jpg done");
    }

    //slowest
    public function svg()
    {
        //convert pdf to svg
        $pdf2svg = exec("which pdf2svg");

        if (!$pdf2svg)
            throw new \Exception ('Library missing (4)');

        $cmd = $pdf2svg . " " . $this->pdfPath . " " . $this->svgPath;
        Log::info($cmd);
        $result = exec($cmd);
        $this->cloud->svg = Storage::cloud()->putFile('updf/svg', $this->svgPath);
        Log::info("Svg done");
    }

}
