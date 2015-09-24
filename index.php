<?php

// default values
$dir = __DIR__;
$slashPos = strrpos($dir, '/');
$dir = substr($dir, 0, $slashPos + 1 );
$filename = $dir.'your_project/db/mydb.mwb';
$outDir   = $dir.'your_project/src/AppBundle/Entity/';
$html = "";

// show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'util.php';

// enable autoloading of classes
$html = autoload($html);

use \MwbExporter\Formatter\Doctrine1\Yaml\Formatter AS D1Y;
use \MwbExporter\Formatter\Doctrine2\Annotation\Formatter AS D2A;
use \MwbExporter\Formatter\Doctrine2\Yaml\Formatter AS D2Y;
use \MwbExporter\Formatter\Doctrine2\ZF2InputFilterAnnotation\Formatter AS D2Z;
use \MwbExporter\Formatter\Propel1\Xml\Formatter AS P1X;
use \MwbExporter\Formatter\Propel1\Yaml\Formatter AS P1Y;
use \MwbExporter\Formatter\Sencha\ExtJS3\Formatter AS SE3;
use \MwbExporter\Formatter\Sencha\ExtJS4\Formatter AS SE4;
use \MwbExporter\Formatter\Zend\DbTable\Formatter AS ZD;
use \MwbExporter\Formatter\Zend\RestController\Formatter AS ZR;

// setup modes
$mode = array(
  'doctrine1.yaml' => 'doctrine1-yaml',
  'doctrine2.annotation' => 'doctrine2-annotation',
  'doctrine2.yaml' =>  'doctrine2-yaml',
  'doctrine2.zf2inputfilter' => 'doctrine2-zf2inputfilterannotation',
  'propel.xml' => 'propel1-xml',
  'propel.yaml' => 'propel1-yaml',
  'sencha.extjs3' => 'sencha-extjs3',
  'sencha.extjs4' => 'sencha-extjs4',
  'zend.dbtable' => 'zend-dbtable',
  'zend.restcontroller' => 'zend-restcontroller'
);

// IF POST
if(isset($_POST['input']) && isset($_POST['output']) && isset($_POST['mode']) ){

  // set inputs
  $filename = $_POST['input'];
  $outDir = $_POST['output'];

  // formatter setup
  $setup = array(
    'doctrine1.yaml' => array(
        D1Y::CFG_USE_LOGGED_STORAGE            => true,
        D1Y::CFG_INDENTATION                   => 2,
        D1Y::CFG_FILENAME                      => '%entity%.%extension%',
        D1Y::CFG_EXTEND_TABLENAME_WITH_SCHEMA  => false,
    ),
    'doctrine2.annotation' =>  array(
        D2A::CFG_USE_LOGGED_STORAGE        => true,
        D2A::CFG_INDENTATION               => 4,
        D2A::CFG_FILENAME                  => '%entity%.%extension%',
        D2A::CFG_ANNOTATION_PREFIX         => 'ORM\\',
        D2A::CFG_BUNDLE_NAMESPACE          => 'AppBundle',
        D2A::CFG_ENTITY_NAMESPACE          => 'Entity',
        D2A::CFG_REPOSITORY_NAMESPACE      => '',
        D2A::CFG_AUTOMATIC_REPOSITORY      => true,
        D2A::CFG_SKIP_GETTER_SETTER        => false,
    ),
    'doctrine2.yaml' =>  array(
        D2Y::CFG_USE_LOGGED_STORAGE            => true,
        D2Y::CFG_INDENTATION                   => 4,
        D2Y::CFG_FILENAME                      => '%entity%.orm.%extension%',
        D2Y::CFG_BUNDLE_NAMESPACE              => '',
        D2Y::CFG_ENTITY_NAMESPACE              => '',
        D2Y::CFG_REPOSITORY_NAMESPACE          => '',
        D2Y::CFG_EXTEND_TABLENAME_WITH_SCHEMA  => false,
        D2Y::CFG_AUTOMATIC_REPOSITORY          => true,
    ),
    'doctrine2.zf2inputfilter' => array(
        D2Z::CFG_USE_LOGGED_STORAGE        => true,
        D2Z::CFG_INDENTATION               => 4,
        D2Z::CFG_BUNDLE_NAMESPACE          => 'AppBundle',
        D2Z::CFG_ENTITY_NAMESPACE          => 'Entity',
        D2Z::CFG_AUTOMATIC_REPOSITORY      => true,
        D2Z::CFG_SKIP_GETTER_SETTER        => false,
    ),
    'propel.xml' => array(
        P1X::CFG_USE_LOGGED_STORAGE  => true,
        P1X::CFG_INDENTATION         => 4,
        P1X::CFG_ADD_VENDOR          => false,
        P1X::CFG_NAMESPACE           => 'Acme\Namespace',
    ),
    'propel.yaml' => array(
        P1Y::CFG_USE_LOGGED_STORAGE  => true,
        P1Y::CFG_INDENTATION         => 2,
    ),
    'sencha.extjs3' => array(
        SE3::CFG_USE_LOGGED_STORAGE  => true,
        SE3::CFG_INDENTATION         => 4,
        SE3::CFG_FILENAME            => 'JS/%schema%/%entity%.%extension%',
        SE3::CFG_CLASS_PREFIX        => 'SysX.App',
        SE3::CFG_PARENT_CLASS        => 'SysX.Ui.App',
    ),
    'sencha.extjs4' => array(
        SE4::CFG_USE_LOGGED_STORAGE  => true,
    ),
    'zend.dbtable' => array(
        ZD::CFG_USE_LOGGED_STORAGE      => true,
        ZD::CFG_INDENTATION             => 4,
        ZD::CFG_FILENAME                => 'DbTable/%schema%/%entity%.%extension%',
        ZD::CFG_TABLE_PREFIX            => 'Application_Model_DbTable_',
        ZD::CFG_PARENT_TABLE            => 'Zend_Db_Table_Abstract',
        ZD::CFG_GENERATE_DRI            => false,
        ZD::CFG_GENERATE_GETTER_SETTER  => false,
    ),
    'zend.restcontroller' => array(
        ZR::CFG_USE_LOGGED_STORAGE  => true,
        ZR::CFG_INDENTATION         => 4,
        ZR::CFG_FILENAME            => '%entity%.%extension%',
        ZR::CFG_TABLE_PREFIX        => '',
        ZR::CFG_PARENT_TABLE        => 'Zend_Rest_Controller',
    )
  );

  // lets do it
  $html = export($mode[$_POST['mode']], $setup[$_POST['mode']], $filename, $outDir);

}


?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>MySQlWorkbench Exporter</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>
    
    <div class="container">
      <h2>Select db file, export location and export ORM file type</h2>

      <form action="index.php" method="POSt">
        <label>MySQlWorkbench file:</label>
        <input type="text" name="input" value="<?php echo $filename; ?>" class="form-control input-lg"/>
        <p></p>

        <label>Output path:</label>
        <input type="text" name="output" value="<?php echo $outDir; ?>" class="form-control input-lg"/>
        <p></p>
        
        <label>Export type:</label>
        <select class="form-control input-lg" name="mode">
          <?php
            foreach( $mode as $key => $value ){
              echo '<option value="'.$key.'">'.$value.'</option>';
            }
          ?>
        </select>
        <p></p>
        <hr/>

        <button class="btn btn-lg btn-success">send</button>
      </form>


      <div>
        <?php echo $html; ?>
      </div>

   </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>