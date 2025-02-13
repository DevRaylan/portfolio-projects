<?php
namespace Geral\Model;

use \Geral\Model\Exception\ErrorException;

/**
 * Abstrai o carregamento da biblioteca WideImage.
 * Utilização:
 * - Criar uma instência de wideimage sem uma imagem.
 *      $image = WideImage::createInstance();
 *
 * - Criar a partir de uma imagem existente
 *      $imageFile = <caminho do arquivo imagem>
 *      $image = WideImage::createInstance($imageFile);
 */
abstract class WideImage
{
    const DIR_LIB = DIR_ROOT.DIR_LIBS.'/wideimage';
    const DIR_FONTS = self::DIR_LIB.'/fonts';

    // Cria uma nova instância com ou sem arquivo pré-carregado
    static public function createInstance(String $imageFile = null)
    {
        require_once(self::DIR_LIB.'/WideImage.php');

        if(!empty($imageFile)) {
            if(!is_file($imageFile)) {
                throw new ErrorException('Arquivo de imagem "'.$imageFile.'" não existe.');
            }

            return \WideImage::load($imageFile);
        } else {
            return new \WideImage();
        }
    }

    // Retorna o caminho da fonte em /libs/wideimage/fonts/<fontName>.ttf
    static public function getFontPath($fontName)
    {
        $file = self::DIR_FONTS.'/'.$fontName.'.ttf';

        if(!file_exists($file)) {
            throw new ErrorException('Fonte "'.$fontName.'" não existe na biblioteca do WideImage.');
        }

        return $file;
    }

}