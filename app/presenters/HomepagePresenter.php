<?php

namespace App\Presenters;

use Nette,
    App\Model,
    App\libs\PHPExcel\PHPExcel;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    private $database;
    private $new = 0;

    public function __construct(Nette\Database\Context $database) {
        parent::__construct();
        $this->database = $database;
    }

    public function renderDefault($new = 0, $edit = 0) {
        $this->template->persons = $this->database->table('persons')->fetchAll();
        $this->new = $new;
        $this->template->new = $new;
        $this->template->edit = $edit;
        $this->template->j = 1;
    }

    public function renderCalculate($amount) {
        $this->template->amount = $amount;
        $persons = $this->database->table('persons')->order('id');
        $result = array();
        $whole['500'] = 0;
        $whole['200'] = 0;
        $whole['100'] = 0;
        $whole['50'] = 0;
        $whole['20'] = 0;
        $whole['10'] = 0;
        $whole['5'] = 0;
        $whole['2'] = 0;
        $whole['1'] = 0;
        $whole['0.5'] = 0;
        $whole['0.2'] = 0;
        $whole['0.1'] = 0;
        $whole['0.05'] = 0;
        $whole['0.02'] = 0;
        $whole['0.01'] = 0;
        foreach ($persons as $person) {
            $p = new \App\Model\Person($person->name, $person->part1, $person->part2, $amount);
            $result[$person->id] = $p;
            $whole['500'] = $whole['500'] + $p->banknotes['500'];
            $whole['200'] = $whole['200'] + $p->banknotes['200'];
            $whole['100'] = $whole['100'] + $p->banknotes['100'];
            $whole['50'] = $whole['50'] + $p->banknotes['50'];
            $whole['20'] = $whole['20'] + $p->banknotes['20'];
            $whole['10'] = $whole['10'] + $p->banknotes['10'];
            $whole['5'] = $whole['5'] + $p->banknotes['5'];
            $whole['2'] = $whole['2'] + $p->banknotes['2'];
            $whole['1'] = $whole['1'] + $p->banknotes['1'];
            $whole['0.5'] = $whole['0.5'] + $p->banknotes['0.5'];
            $whole['0.2'] = $whole['0.2'] + $p->banknotes['0.2'];
            $whole['0.1'] = $whole['0.1'] + $p->banknotes['0.1'];
            $whole['0.05'] = $whole['0.05'] + $p->banknotes['0.05'];
            $whole['0.02'] = $whole['0.02'] + $p->banknotes['0.02'];
            $whole['0.01'] = $whole['0.01'] + $p->banknotes['0.01'];
        }
        $this->template->persons = $result;
        $this->template->whole = $whole;
        $this->template->j = 1;
        $this->createXML($result, $whole);
    }

    public function createXML($persons, $whole) {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator('Lauro')
                ->setLastModifiedBy("Lauro")
                ->setTitle("Vysledky")
                ->setSubject("Vysledky")
                ->setDescription("Toto su vysledky tejto stranky");
        $ews = $objPHPExcel->getSheet(0);
        $ews->setTitle('Vlastnici');

        //formatovanie a nastavenie hlavicky
        $ews->setCellValue('a1', 'MENO'); // Sets cell 'a1' to value 'ID 
        $ews->setCellValue('b1', 'PODIEL');
        $ews->setCellValue('c1', 'SUMA');
        $header = 'a1:c1';
        $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        for ($col = ord('a'); $col <= ord('c'); $col++) {
            $ews->getColumnDimension(chr($col))->setAutoSize(true);
        }
        $ews->getStyle($header)->applyFromArray($style);
        $i = 2;

        //sheet with persons
        foreach ($persons as $person) {
            $ews->setCellValueByColumnAndRow(0, $i, $person->name);
            $ews->setCellValueByColumnAndRow(1, $i, $person->part1 . '/' . $person->part2);
            $ews->setCellValueByColumnAndRow(2, $i, $person->amount);
            $i++;
        }
        //sheet with banknotes
        $ews2 = new \PHPExcel_Worksheet($objPHPExcel, 'Bankovky');
        $objPHPExcel->addSheet($ews2, 1);
        $ews2->setTitle('Summary');
        for ($col = ord('a'); $col <= ord('d'); $col++) {
            $ews2->getColumnDimension(chr($col))->setAutoSize(true);
        }


        $ews->getStyle('a1')->applyFromArray($style);
        $ews2->setCellValueByColumnAndRow(0, 1, 'Ceľkový prehľad');
        $ews2->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );

        $ews2->setCellValueByColumnAndRow(0, 2, 'Bankovky');
        $ews2->getStyle('A2')->getAlignment()->setTextRotation(90)
        ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $ews2->setCellValueByColumnAndRow(0,8,'Mince');
        $ews2->getStyle('A8')->getAlignment()->setTextRotation(90)
        ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $ews2->mergeCells('A8:A16');
        $ews2->mergeCells('A1:D1');
        $ews2->mergeCells('A2:A7');

        $ews2->setCellValueByColumnAndRow(1, 2, '500€');
        $ews2->setCellValueByColumnAndRow(2, 2, $whole['500']);
        $ews2->setCellValueByColumnAndRow(3, 2, $whole['500'] * 500);

        $ews2->setCellValueByColumnAndRow(1, 3, '200€');
        $ews2->setCellValueByColumnAndRow(2, 3, $whole['200']);
        $ews2->setCellValueByColumnAndRow(3, 3, $whole['200'] * 200);

        $ews2->setCellValueByColumnAndRow(1, 4, '100€');
        $ews2->setCellValueByColumnAndRow(2, 4, $whole['100']);
        $ews2->setCellValueByColumnAndRow(3, 4, $whole['100'] * 100);

        $ews2->setCellValueByColumnAndRow(1, 5, '50€');
        $ews2->setCellValueByColumnAndRow(2, 5, $whole['50']);
        $ews2->setCellValueByColumnAndRow(3, 5, $whole['50'] * 50);
        
        $ews2->setCellValueByColumnAndRow(1, 6, '20€');
        $ews2->setCellValueByColumnAndRow(2, 6, $whole['20']);
        $ews2->setCellValueByColumnAndRow(3, 6, $whole['20'] * 20);

        $ews2->setCellValueByColumnAndRow(1, 7, '10€');
        $ews2->setCellValueByColumnAndRow(2, 7, $whole['10']);
        $ews2->setCellValueByColumnAndRow(3, 7, $whole['10'] * 10);

        $ews2->setCellValueByColumnAndRow(1, 8, '5€');
        $ews2->setCellValueByColumnAndRow(2, 8, $whole['5']);
        $ews2->setCellValueByColumnAndRow(3, 8, $whole['5'] * 5);
        
        $ews2->setCellValueByColumnAndRow(1, 9, '2€');
        $ews2->setCellValueByColumnAndRow(2, 9, $whole['2']);
        $ews2->setCellValueByColumnAndRow(3, 9, $whole['2'] * 2);
        
        $ews2->setCellValueByColumnAndRow(1, 10, '1€');
        $ews2->setCellValueByColumnAndRow(2, 10, $whole['1']);
        $ews2->setCellValueByColumnAndRow(3, 10, $whole['1'] * 1);
        
        $ews2->setCellValueByColumnAndRow(1, 11, '50¢');
        $ews2->setCellValueByColumnAndRow(2, 11, $whole['0.5']);
        $ews2->setCellValueByColumnAndRow(3, 11, $whole['0.5'] * 0.5);
        
        $ews2->setCellValueByColumnAndRow(1, 12, '20¢');
        $ews2->setCellValueByColumnAndRow(2, 12, $whole['0.2']);
        $ews2->setCellValueByColumnAndRow(3, 12, $whole['0.2'] * 0.2);
        
        $ews2->setCellValueByColumnAndRow(1, 13, '10¢');
        $ews2->setCellValueByColumnAndRow(2, 13, $whole['0.1']);
        $ews2->setCellValueByColumnAndRow(3, 13, $whole['0.1'] * 0.1);
        
        $ews2->setCellValueByColumnAndRow(1, 14, '5¢');
        $ews2->setCellValueByColumnAndRow(2, 14, $whole['0.05']);
        $ews2->setCellValueByColumnAndRow(3, 14, $whole['0.05'] * 0.05);
        
        $ews2->setCellValueByColumnAndRow(1, 15, '2¢');
        $ews2->setCellValueByColumnAndRow(2, 15, $whole['0.02']);
        $ews2->setCellValueByColumnAndRow(3, 15, $whole['0.02'] * 0.02);
        
        $ews2->setCellValueByColumnAndRow(1, 16, '1¢');
        $ews2->setCellValueByColumnAndRow(2, 16, $whole['0.01']);
        $ews2->setCellValueByColumnAndRow(3, 16, $whole['0.01'] * 5);
        
        $ews2->setCellValueByColumnAndRow(3, 17, '=SUM(D2:D16)');



        // save
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('Vysledky.xlsx');
    }

    public function handleAddNew($new) {
        if ($this->isAjax()) {
            $this->new = $new;
            $this->redrawControl('news');
        } else {
            $this->redirect('this', $new);
        }
    }

    public function createComponentCalculate() {
        $form = new Nette\Application\UI\Form;
        $form->addText('value')
                ->addRule($form::FILLED, 'Zadajte zisk/stratu!');
        $form->addSubmit('send', 'Vypočítať');
        $form->onSuccess[] = array($this, 'calculate');
        return $form;
    }

    public function calculate($form) {
        $persons = $this->database->table('persons')->order('id');
        $check = 0;
        foreach ($persons as $person) {
            $check = $check + $person->part1 / $person->part2;
        }
        if ($check <> 1) {
            $form->addError('Súčet podielov nie je rovný jednej!!');
        } else {
            $this->redirect('Homepage:calculate', $form->getValues()->value);
        }
    }

    public function handleDelete($id) {
        $this->database->table('persons')->where('id=?', $id)->delete();
        $this->redirect('this');
    }

    public function createComponentPersons() {
        return new Nette\Application\UI\Multiplier(function ($personId) {
            $form = new Nette\Application\UI\Form;
            $form->addText('name')
                    ->addRule($form::FILLED, 'Zadajte meno!');
            $form->addText('part1')->addRule($form::INTEGER, 'Zadajte celé číslo');
            $form->addText('part2')->addRule($form::INTEGER, 'Zadajte celé číslo');
            $form->addHidden('itemId', $personId);
            $form->addSubmit('send', 'Potvrdiť');
            $form->onSuccess[] = array($this, 'addPerson');
            return $form;
        });
    }

    public function createComponentEdit() {
        $form = new Nette\Application\UI\Form;
        $form->addText('name')
                ->addRule($form::FILLED, 'Zadajte meno!');
        $form->addText('part1')->addRule($form::INTEGER, 'Zadajte celé číslo');
        $form->addText('part2')->addRule($form::INTEGER, 'Zadajte celé číslo');
        $form->addSubmit('send', 'Potvrdiť');
        $form->onSuccess[] = array($this, 'EditPerson');
        return $form;
    }

    public function editPerson($form) {
        $values = $form->getValues();
        if ($values->part1 > $values->part2) {
            $form->addError('Pomer je neplatný!');
        } else {
            $this->database->table('persons')->where('id=?', $this->getParameter('edit'))->update(array(
                'name' => $values->name,
                'part1' => $values->part1,
                'part2' => $values->part2
            ));
            if ($this->getParameter('new') == 0) {
                $this->redirect('default', 0);
            }
            $this->redirect('default', $this->getParameter('new') - 1);
        }
    }

    public function addPerson($form) {
        $values = $form->getValues();
        if ($values->part1 > $values->part2) {
            $form->addError('Pomer je neplatný!');
        } else {
            $this->database->table('persons')->insert(array(
                'name' => $values->name,
                'part1' => $values->part1,
                'part2' => $values->part2
            ));
            if ($this->getParameter('new') == 0) {
                $this->redirect('default', 0);
            }
            $this->redirect('default', $this->getParameter('new') - 1);
        }
    }

}
