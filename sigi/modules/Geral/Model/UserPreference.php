<?php
/**
 * Esta classe fornece o serviço de gerenciamento de parâmetros de usuários.
 * A instância sempre define os valores dos atributos cpf e app de acordo com
 * os fornecidos na instanciação ou utiliza o padrão.
 */
namespace Geral\Model;

use Geral\Model\Exception\ErrorException;
use Geral\Model\Factory\UserPreferenceFactory;

class UserPreference
{
    /**
     * Aplicação que contém os parâmetros.
     * Se não for fornecida na instanciação, então é utilizado a aplicação da URL.
     */
    private $app;

    // Cpf do usuário para gerenciamento dos parâmetros
    private $cpf;

    // Parâmetros da aplicação
    private $preferences;

    public function __construct(String $cpf, String $app = null)
    {
        $this->app = empty($app) ? $GLOBALS['url']->getNameModule() : $app;
        $this->cpf = $cpf;
        $this->preferences = \Geral\Model\Config::getValue('userPreferences');
    }

    /**
     * Retorna os valores de um parâmetro.
     * 
     * Se o parâmetro pode ter mais de um valor, então é retornado um array, vazio ou com vários elementos.
     */
    public function &getPreference($preference)
    {
        $this->checkPreferenceSetting($preference);

        $preferenceValues = UserPreferenceFactory::getPreferencesByCpf($this->app, $this->cpf, $preference);

        if($this->isMultiple($preference)) {
            $values = [];

            foreach($preferenceValues as $preferenceValue) {
                $values[] = $preferenceValue->getValue();
            }
        } else {
            $values = !empty($preferenceValues) ? current($preferenceValues)->getValue() : null;
        }

        return $values;
    }

    // Retorna todos os parâmetros do usuário.
    public function &getPreferences()
    {
        $preferences = [];

        foreach(array_keys($this->preferences) as $preference) {
            $preferences[$preference] = $this->getPreference($preference);

            if(empty($preferences[$preference])) unset($preferences[$preference]);
        }

        return $preferences;
    }

    // Salva os valores de um parâmetro.
    public function setPreference(String $preference, $value)
    {
        $this->checkPreferenceSetting($preference);

        // A factory sempre trata como array, sendo múltiplo ou não.
        UserPreferenceFactory::update($this->app, $this->cpf, $preference, (!is_array($value) ? [$value] : $value));
    }

    // Remove um parâmetro.
    public function removePreference(String $preference)
    {
        UserPreferenceFactory::removeAllPreferenceValues($this->app, $this->cpf, $preference);
    }

    // Verifica se o usuário definiu um valor para a preferência.
    public function preferenceExists($preference) {
        $preferenceValues = UserPreferenceFactory::getPreferencesByCpf($this->app, $this->cpf, $preference);

        return !is_null($preferenceValues);
    }

    // Retorna várias instâncias de preferências de usuários.
    public static function getInstances(Array $cpfs, String $app = null)
    {
        $userPreferences = [];

        foreach($cpfs as $cpf) {
            $userPreferences[] = new self($cpf, $app);
        }

        return $userPreferences;
    }

    // Verifica se o parâmetro suporta múltiplos valores
    private function isMultiple($preference)
    {
        return $this->preferences[$preference]['multiplosValores'];
    }

    // Verificar se o parâmetro está configurado no config da aplicação.
    private function checkPreferenceSetting($preference) {
        if(!isset($this->preferences[$preference])) {
            throw new ErrorException('O parâmetro "'.$preference.'" não está configurado no config da aplicação,
                os parâmetros aceitos são: "'.implode('", "',array_keys($this->preferences)).'"');
        }
    }
}