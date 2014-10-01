<?php

class XtTemplate {

    public static function merge(//mescla os dados com o template
            $arr//(array) os dados para substituir no template
            ,$tpl//o template
            ){//return string|false
        global $xterror;
        if(!is_array($arr)){
            $xterror = 1;
            return false;
        }

        $merged = $tpl;
        foreach($arr as $key => $content){
            if(is_array($content)){
                $delimiter = '{{'.$key.':}}';
                $tmp0 = explode($delimiter, $merged);
                $delimiter = '{{:'.$key.'}}';
                $tmp1 = explode($delimiter, $tmp0[1]);
                $str = $tmp1[0];
                $submerged = '';
                foreach($content as $rows){
                    $submerged .= XtTemplate::merge($rows, $str);
                }
                $search = '{{'.$key.':}}'.$str.'{{:'.$key.'}}';
                $merged = str_replace($search, $submerged, $merged);
            }else{
                $merged = str_replace('{{'.$key.'}}', $content, $merged);
            }
        }
        return $merged;
    }//eof merge()

}//eof XtTemplate class

?>
