<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
class Aitoc_Aitsys_Model_Rewriter_Inheritance extends Aitoc_Aitsys_Model_Rewriter_Abstract
{
    protected $_contentArray = array();
    protected $_baseClass = '';
    protected $_classInh = '';
    protected $_baseClasses = array();

    /**
     * @return mixed
     */
    public function loadOrderConfig()
    {
        return $this->tool()->db()->getConfigValue('aitsys_rewriter_classorder', array());
    }

    /**
     * Creates inheritance array
     *
     * @param $rewriteClasses
     * @param $baseClass
     * @param bool $useOrdering
     * @return array|bool
     */
    public function build(&$rewriteClasses, $baseClass, $useOrdering = true)
    {
        $this->_baseClass = $baseClass;
        $this->_baseClasses = array();

        $inheritedClasses = array();
        $orderedInheritance = array();
        //check extend files
        foreach($rewriteClasses as $class)
        {
            $this->_classInh = $class;
            $this->_findInheriatanceClass($class, $rewriteClasses);
        }
        krsort($rewriteClasses);
        $rewriteClasses = array_values($rewriteClasses);
        $i = 0;
        while ($i < count($rewriteClasses))
        {
            $inheritedClasses[$rewriteClasses[$i]] = isset($rewriteClasses[++$i]) ? $rewriteClasses[$i] : $baseClass;
        }

        if(count($this->_baseClasses) > 0)
        {
            $inheritedClasses['_baseClasses'] = $this->_baseClasses;
            $inheritedClasses['_baseClasses']['__topClass'] = $this->_getBaseClass();
        }
        // reversing to make it read classed in order of existence
        $inheritedClasses = array_reverse($inheritedClasses, true);

        // sorting in desired order
        $order = $this->loadOrderConfig();
        if (!$order)
        {
            $order = array();
        }

        if(count($rewriteClasses) <= 1)
        {
            return false;
        }
        if (!isset($order[$baseClass]) || !$this->_aithelper('Rewriter')->validateSavedClassConfig($order[$baseClass], $rewriteClasses))
        {
            $i = 0;
            $order[$baseClass] = array();
            foreach ($rewriteClasses as $class)
            {
                $order[$baseClass][$class] = ++$i;
            }
        }

        /* Check encoded files */
        $encoded = array();
        if (isset($order[$baseClass])) {
            $classes = array_flip($order[$baseClass]);
            ksort($classes);
            $classes = array_values($classes);

            $classModel = new Aitoc_Aitsys_Model_Rewriter_Class();
            foreach ($classes as $k => $class) {
                if ($classModel->isEncodedClassFile($class)) {
                    $encoded[] = $class;
                    unset($classes[$k]);
                }
            }
            $classes = array_merge($classes, $encoded);
            $i = 0;
            $order[$baseClass] = array();
            foreach ($classes as $class)
            {
                $order[$baseClass][$class] = ++$i;
            }
        }
        if ($useOrdering && isset($order[$baseClass]))
        {
            $orderedClasses = array_flip($order[$baseClass]);
            ksort($orderedClasses);
            $orderedClasses = array_values($orderedClasses);
            
            $i             = 0;
            $replaceClass = array();
            while ($i < count($orderedClasses))
            {
                $contentsFromClass = $orderedClasses[$i];
                if (0 == $i && $orderedClasses[$i] != $rewriteClasses[$i])
                {
                    $parentClass = $rewriteClasses[$i];
                    $replaceClass[$rewriteClasses[$i]] = $orderedClasses[$i];
                } 
                else 
                {
                    $parentClass = $orderedClasses[$i];
                    if (isset($replaceClass[$parentClass]))
                    {
                        $parentClass = $replaceClass[$parentClass];
                    }
                }
                if (isset($orderedClasses[$i+1]))
                {
                    $childClass = $orderedClasses[$i+1];
                    if (isset($replaceClass[$childClass]))
                    {
                        $childClass = $replaceClass[$childClass];
                    }
                } else 
                {
                    $childClass = $this->_getBaseClass();
                }
                $orderedInheritance[] = array(
                    'contents'  => $contentsFromClass,
                    'parent'    => $parentClass,
                    'child'     => $childClass,
                    'encoded'   => in_array($contentsFromClass, $encoded),
                    'content'   => $this->_contentArray[$contentsFromClass]
                );
                $i++;
            }
            if ($orderedInheritance)
            {
                krsort($orderedInheritance);
                $inheritedClasses = $orderedInheritance;
            }
        }

        return $inheritedClasses;
    }

    /**
     * @param $rewriteClass
     * @param $baseClass
     * @return array
     */
    public function buildAbstract($rewriteClass, $baseClass)
    {
        $inheritedClasses = array();
        $inheritedClasses[] = array(
            'contents'  => $baseClass,
            'parent'    => $rewriteClass,
            'child'     => '', // empty to keep current
        );
        $inheritedClasses[] = array(
            'contents'  => $rewriteClass,
            'parent'    => $baseClass,
            'child'     => $rewriteClass,
        );
        return $inheritedClasses;
    }

    /**
     * @param $class
     * @param $array
     * @return bool
     */
    protected function _findInheriatanceClass($class, &$array)
    {
        $classModel = new Aitoc_Aitsys_Model_Rewriter_Class();
        $content = $classModel->getContents($class);

        if(empty($content))
        {
            return false;
        }
        if(in_array($class, $array))
        {
            //delete static AITOC rewrite. Not use preg_replace because 100k limit
            if((strpos($class, 'Aitoc') === 0 || strpos($class, 'AdjustWare') === 0) && strpos($content, 'AITOC static rewrite inserts start') !== false)//preg_match('/\$meta=\%(.*)?\%/',$content, $maches))
            {
                $maches = array();
                preg_match('/\/\* default extends start \*\/(\n)?(\s+)?class(\s+)?'.$class.'_Aittmp(\s+)extends(\s+)?([^\s{]+)?(\s+)?{}(\n)?(\s+)?\/\* default extends end \*\//',$content, $maches);

                //$content = preg_replace('/\/\* AITOC static rewrite inserts start \*\/(.|\n)*\/\* AITOC static rewrite inserts end \*\/(\n)?/', '', $content, 1);
                //$content = preg_replace('/(class)(\s+)?'.$class.'(\s+)(extends)?(\s+)?'.$class.'_Aittmp/', 'class '.$class.' extends '.$maches[6], $content, 1);
                $content = substr($content, strpos($content, '/* AITOC static rewrite inserts end */'));
                $content = str_replace('/* AITOC static rewrite inserts end */', '', $content);
                $content = str_replace('class '.$class.' extends '.$class.'_Aittmp', 'class '.$class.' extends '.$maches[6], $content);
            }

            $this->_contentArray[$class] = $content;
        }
        $this->_checkClassByContent($class, $array, $content);
    }

    /**
     * @param $class
     * @param $array
     * @param $content
     * @return bool
     */
    protected function _checkClassByContent($class, &$array, $content)
    {
        $pregClassArray = array();
        if(preg_match_all('/' . $class . '(\s+)(extends)(\s+)?([^\s{]+)?/', $content, $pregClassArray))
        {
            $classExtendArray = $pregClassArray[4];
            foreach($classExtendArray as $classExtend)
            {
                list($vendor, $name) = explode('_',$classExtend,3);
                if(!Mage::helper('core')->isModuleEnabled($vendor.'_'.$name))
                {
                    continue;
                }
                if($vendor != 'Mage')
                {
                    if(($key = array_search($classExtend, $array)) !== false) {
                        unset($array[$key]);
                        $this->_baseClasses[$this->_classInh] = $classExtend;
                        $this->_classInh = $classExtend;
                    }
                    if(!$this->_checkClassByContent($classExtend, $array, $content))
                    {
                        $this->_findInheriatanceClass($classExtend, $array);
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function _getBaseClass()
    {

        if(count($this->_baseClasses) > 0 && $this->_baseClass != $this->_classInh)
        {
            foreach($this->_baseClasses as $key=>$value)
            {
                $this->_unsetBaseClass($key, $value);
            }
            $this->_classInh = reset($this->_baseClasses);
            $this->_baseClass = $this->_classInh;
        }

        return $this->_baseClass;
    }

    /**
     * @param $base
     * @param $class
     */
    protected function _unsetBaseClass($base, $class)
    {
        if(!empty($this->_baseClasses[$class])){
            $this->_unsetBaseClass($class, $this->_baseClasses[$class]);
        }
        foreach($this->_baseClasses as $key=>&$value)
        {
            if($key != $base && $value == $class){
                $value = $base;
            }
            if($value == $base && !empty($this->_baseClasses[$base])){
                unset($this->_baseClasses[$base]);
            }
        }
    }
}