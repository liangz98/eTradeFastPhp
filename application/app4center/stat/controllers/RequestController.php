<?php

class RequestController extends Seed_Controller_Action4Admin
{
    function pageurlAction()
    {
        $hitsM = new Stat_Model_Hits('stat');
        
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        
        $path = $f1->filter($this->_request->getParam('path'));
        $parentPath = $f1->filter($this->_request->getParam('parentPath'));
        $nodeid = $f1->filter($this->_request->getParam('nodeid'));
        if(empty($path) || empty($nodeid)) {return;}
        $fullPath = $parentPath.$path;
        $conditions = array(
            "resource like '{$fullPath}%'" => NULL
        );
        
        $depth = count(explode('-', $nodeid));
        $split = strpos($path, '?') !== false ? '&' : '=';
		$statList = $hitsM->fetchHitGroup($conditions, $depth + 1, $split);
        $output = '';
		foreach ($statList as $k => $stat) {
            $ttid = $nodeid.'-'.($k+1);
            if($stat['res'] == $fullPath) {$stat['res'] .= '/index.html';}
            $stat['res'] = str_replace($fullPath, '', $stat['res']);
            $deep = count(explode('/', trim($stat['res'], '/')));
            if($deep <= 1) {
                $deep = count(explode('?', trim($stat['res'], '/')));
            }
            $output .= '<tr data-tt-id="'.$ttid.'" data-tt-parent-id="'.$nodeid.'" parentPath="'.$fullPath.'" datatype="'.$nodeid.'">';
            if($deep > 1) {
                $output .= '<td style="text-align:left;height:20px;"><span class="folder">'.$stat['res'].'</span></td>'.
                        '<td>'.$stat['total'].'</td></tr><tr data-tt-id="'.$ttid.'-1" data-tt-parent-id="'.$ttid.'">';
            } else {
                $output .= '<td style="text-align:left;height:20px;"><span class="file">'.$stat['res'].'</span></td>'.
                        '<td>'.$stat['total'].'</td>';
            }
            $output .= '</tr>';
        }
        exit($output);
    }
}