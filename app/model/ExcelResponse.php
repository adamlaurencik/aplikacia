<?php
namespace App\Model;

use Nette\Application\IResponse;
use Nette\Object;
use Nette;
use Nette\Utils\Strings;

class ExcelResponse extends Object implements  IResponse {

    /** @var \PHPExcel */
    private $excelObject;
    /** @var string */
    private $filename;

    public function __construct(\PHPExcel $excelObject, $filename)
    {
        $this->excelObject = $excelObject;
        $this->filename = $filename;
    }


    /**
     * Sends response to output.
     * @return void
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
    {
        $httpResponse->setContentType('application/force-download');
        $httpResponse->setHeader('Content-Disposition', 'attachment;filename='.$this->filename);
        $httpResponse->setHeader('Content-Transfer-Encoding', 'binary');

        $writer = new \PHPExcel_Writer_Excel5($this->excelObject);
        $writer->save('php://output');
    }
}
?>