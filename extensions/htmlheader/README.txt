Configuration notes for htmlHeader:

At first you need to place the htmlheader:gethead Taglib in your <head></head>
section:
----------------
<head>
    <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="gethead" />
    <htmlheader:gethead />
</head>
-----------------

Then you can use the other taglibs for adding javascripts, stylesheets and titles
to your <head> section whereever you want. For meta-refrehs, there can't be used a taglib,
see below for more information.

Examples:
----------------------------------
// Adding taglibs to your template
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addcss" />
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addjs" />
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addtitle" />

// Using taglibs
<htmlheader:addjs namespace="sites::example::pres::frontend::static::js" filename="jquery.min" />
<htmlheader:addjs url="http://static/" folder="js::anything" filename="jquery.min" rewriting="false" fcaction="false" />
<htmlheader:addcss namespace="sites::example::pres::frontend::static::css" filename="stylesheet" />
<htmlheader:addcss url="http://static/" folder="css::anything" filename="stylesheet" rewriting="false" fcaction="false" />
<htmlheader:addtitle append="false">This is a example title</htmlheader:addtitle>
-----------------------------------

The parameters:
-----------
url: If you want to use an external file, set the url of target server.
folder: If you use an external file, specify folder which contains the file.
namespace: The namespace of the file.
filename: The name of the file, without extension (e.g. '.css', '.js').
rewriting: Optional. Generate url which uses url-rewriting? (Default: Same as application)
fcaction: Optional Use fc action for delivering file? (Default: true)
append: Set append to true if you want to add the given title at the end of the
        already existing title. Otherwise set to false, if you want to overwrite existing titles.
-----------


Using without other taglibs, e.g. in documentcontroller:
You need to place the gethead taglib, but for the other taglibs you can use the
following. (And for meta-refreshs you need to use it)
-----------------------------------
//Get an instance of HtmlHeaderManager:
$HHM = $this->__getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');

// Add a refresh on index.php?test=abc, with a delay of 5 seconds:
$HHM->addRefresh('index.php', 5, array("test" => "abc"));

// Add a title (direct edit the $title variable of HHM)
$HHM->title = "Example title";

// Import css-node class
import('extensions::htmlheader::biz','CssNode');
// Get instance and configure in constructor:
$CssNode = new CssNode($url, $namespace, $filename, $rewriting, $fcaction);
// Add Node to HHM
$HHM->addCss($CssNode);

// Import js-node class
import('extensions::htmlheader::biz','JsNode');
// Get instance and configure in constructor:
$JsNode = new JsNode($url, $namespace, $filename, $rewriting, $fcaction);
// Add Node to HHM
$HHM->addCss($JsNode);
-------------------------------------

That's all you need to know.
Have fun.