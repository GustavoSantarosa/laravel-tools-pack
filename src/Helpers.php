<?php

/**
 * Helper
 * php version 7.4.16
 *
 * @category Helper
 * @package  App\Services\Erp\Nf
 */

if (!function_exists('cleanExceptNumber')) {
    /**
     * Limpa tudo da variavel com exceção numero
     *
     * @param string|null $v1 variavel a ser limpada
     *
     * @return void
     */
    function cleanExceptNumber(string $v1 = null)
    {
        return preg_replace("/[^0-9]/i", "", $v1);
    }
}

if (!function_exists('formatCnpjCpf')) {
    /**
     * Formatar para CPF||CNPJ
     *
     * @param $cnpjCpf cnpj ou cpf a ser formatado
     *
     * @return void
     */
    function formatCnpjCpf($cnpjCpf)
    {
        $cnpjCpf = preg_replace("/\D/", '', $cnpjCpf);

        if ($cnpjCpf == "") {
            return $cnpjCpf = "Não informado";
        }

        if (strlen($cnpjCpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpjCpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpjCpf);
    }
}

if (!function_exists('isSequentialValues')) {
    /**
     * Checa se o Array possui os valores sequenciais
     *
     * @param array $arr array a ser checado
     *
     * @return boolean
     */
    function isSequentialValues(array $arr = []): bool
    {
        sort($arr);

        $indice = 0;
        for ($i = min($arr); $i <= max($arr); $i++) {
            if ($i != $arr[$indice]) {
                return false;
            }
            $indice++;
        }

        return true;
    }
}

if (!function_exists('unaccent')) {
    /**
     * Formatar para CPF||CNPJ
     *
     * @param $str string a ser formatada
     *
     * @return string
     */
    function unaccent($str)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(-)/"), explode(" ", "a A e E i I o O u U n N "), $str);
    }
}

if (!function_exists('removeSpecialChar')) {
    /**
     * Formatar para CPF||CNPJ
     *
     * @param $str string a ser formatada
     *
     * @return string
     */
    function removeSpecialChar($str)
    {
        return preg_replace('/[@\.\;\-\" "]+/', ' ', $str);
    }
}

if (!function_exists('toObject')) {
    /**
     * toStd function
     *
     * @param $arr array a ser transformado em objeto
     * @return object
     */
    function toObject($arr)
    {
        return json_decode(json_encode($arr));
    }
}
