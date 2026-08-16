<?php
namespace Geral\Model;

class UserDataServiceOptions{

    const CARREGA_FOTO = 'carrega_foto';

    private $options;

    public function __construct(){
        $this->options = [
            self::CARREGA_FOTO => false
        ];
    }

    public function setOption($name, $value){
        $this->options[$name] = $value;
    }

    public function hasOption($name){
        return isset($this->options[$name]);
    }

    public function getOption($name, $default=null){
        if($this->hasOption($name)){
            return $this->options[$name];
        }
        return $default;
    }

    public function getOptions(){
        return $this->options;
    }

    public function setCarregaFoto($carregaFoto){
        $this->options[self::CARREGA_FOTO] = $carregaFoto;
    }

    public function getCarregaFoto(){
        return $this->getOption(self::CARREGA_FOTO, false);
    }
}