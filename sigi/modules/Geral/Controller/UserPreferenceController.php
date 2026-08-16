<?php
namespace Geral\Controller;

use Geral\Model\UserPreference;

class UserPreferenceController extends AbstractPublicController
{
    public function __construct( )
    {
        parent::__construct( );
    }

    public function index( )
    {
    }
    
    public function setPreference( )
    {
        $preferencesByCpf = [
            '04171018994' => [
                'defaultApp' => 'Template',
                'linksFavoritos' => ['Link 1', 'link 2'],
            ],
            '22222222222' => [
                'defaultApp' => 'Exemplo',
                'linksFavoritos' => ['Link 1', "2"],
            ],
            '33333333333' => [
                'defaultApp' => 'Exemplo',
                'contratosFavoritos' => [5, 10],
            ]
        ];
        
        foreach($preferencesByCpf as $cpf => $preferenceByCpf) {
            $userPreference = new UserPreference($cpf);

            foreach($preferenceByCpf as $preference => $value) {
                $userPreference->setPreference($preference, $value);
            }
        }
        
        

        $userPreference = new UserPreference('04171018994');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

        $userPreference = new UserPreference('22222222222');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

        $userPreference = new UserPreference('33333333333');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

    }

    public function getPreference( )
    {
        $userPreference = new UserPreference('04171018994');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

        $userPreference = new UserPreference('22222222222');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

        $userPreference = new UserPreference('33333333333');
        var_dump($userPreference->getPreferences());
        echo '<hr />';

    }
}
