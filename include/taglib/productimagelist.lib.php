<?php
!defined('DEDEINC') && exit("403 Forbidden!");
/**
 * 
 *
 * @version        $Id: productimagelist.lib.php 1 9:29 2010年7月6日Z tianya $
 * @package        DedeCMS.Taglib
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
function lib_productimagelist(&$ctag, &$refObj)
{
    global $dsql,$sqlCt;
    $attlist="desclen|80";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    if(!isset($refObj->addTableRow['imgurls'])) return ;
    
    $revalue = '';
    $innerText = trim($ctag->GetInnerText());
    if(empty($innerText)) $innerText = GetSysTemplets('productimagelist.htm');
    
    $dtp = new DedeTagParse();
    $dtp->LoadSource($refObj->addTableRow['imgurls']);
    
    $images = array();
    if(is_array($dtp->CTags))
    {
        foreach($dtp->CTags as $ctag)
        {
            if($ctag->GetName()=="img")
            {
                $row = array();

				$row['imgsrc'] = trim($ctag->GetInnerText());
                $row['text'] = $ctag->GetAtt('text');
				// 解决安装子目录之后，之前存储在数据表图片路径不对的问题 by wengxianhu
				if(!preg_match("#^http:\/\/#i", $row['imgsrc']))
				{
					$pattern_tmp = str_replace('/', '\/', $GLOBALS['cfg_cmspath']);
					if (!preg_match("#^{$pattern_tmp}#i", $row['imgsrc'])) {
						$row['imgsrc'] = $GLOBALS['cfg_cmspath'].trim($ctag->GetInnerText());
						$row['imgsrc'] = str_replace('//', '/', $row['imgsrc']);
					}
				}
				// ------end
                $images[] = $row;
            }
        }
    }
    $dtp->Clear();

    $revalue = '';
    $ctp = new DedeTagParse();
    $ctp->SetNameSpace('field','[',']');
    $ctp->LoadSource($innerText);

    foreach($images as $row)
    {
        foreach($ctp->CTags as $tagid=>$ctag)
        {
            if(isset($row[$ctag->GetName()])){ $ctp->Assign($tagid,$row[$ctag->GetName()]); }
        }
        $revalue .= $ctp->GetResult();
    }
    return $revalue;
}