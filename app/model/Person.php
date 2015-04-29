<?php


namespace App\Model;
/**
 * Description of Person
 *
 * @author Adam
 */
class Person {
    
    public $name;
    public $part1;
    public $part2;
    public $part;
    public $percentage;
    public $amount;
    public $banknotes;
    
    public function __construct($name,$part1,$part2,$total) {
        $this->name=$name;
        $this->part1=$part1;
        $this->part2=$part2;
        $this->part=$part1/$part2;
        $this->percentage=$this->getPercentage();
        $this->amount=round($this->part*$total,2);
        $this->banknotes= $this->getBanknotes();
        
    }
    
    public function getName(){
        return $this->name;
    }
    public function setName($name){
        $this->name=$name;
    }
    public function getPart(){
        return $this->part;
    }
    public function setPart($part){
        $this->part=$part;
    }
    public function getPercentage(){
        return $this->part*100;
    }
    public function getPartAmount($total){
        return $total*part;
    }
    public function getBanknotes(){
        $total=  $this->amount;
        $banknotes=array();
        $banknotes['500']=floor($total/500);
        $total=fmod($total,500);
        $banknotes['200']=floor($total/200);
        $total=fmod($total,200);
        $banknotes['100']=floor($total/100);
        $total=fmod($total,100);
        $banknotes['50']=floor($total/50);
        $total=fmod($total,50);
        $banknotes['20']=floor($total/20);
        $total=fmod($total,20);
        $banknotes['10']=floor($total/10);
        $total=fmod($total,10);
        $banknotes['5']=floor($total/5);
        $total=fmod($total,5);
        $banknotes['2']=floor($total/2);
        $total=fmod($total,2);
        $banknotes['1']=floor($total/1);
        $total=round(fmod($total,1)*100,2);
        $banknotes['0.5']=floor($total/50);
        $total=fmod($total,50);
        $banknotes['0.2']=floor($total/20);
        $total=fmod($total,20);
        $banknotes['0.1']=floor($total/10);
        $total=fmod($total,10);
        $banknotes['0.05']=floor($total/5);
        $total=fmod($total,5);
        $banknotes['0.02']=floor($total/2);
        $total=fmod($total,2);
        $banknotes['0.01']=round($total/1);
        return($banknotes);       
    }
    
}
