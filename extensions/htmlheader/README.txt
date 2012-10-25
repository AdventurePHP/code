Configuration notes for htmlHeader:

At first you need to place the htmlheader:gethead Taglib in your <head></head>
section:
<source lang="html4strict">
<head>
  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_gethead" prefix="htmlheader" name="gethead" />
  <htmlheader:gethead />
</head>
</source>

Then you can use the other taglibs for adding javascripts, stylesheets and titles
to your <head> section whereever you want. For meta-refrehs, there can't be used a taglib,
see below for more information.
Since 1.12 You can use it to deliver packages from the new JsCssPackager.


'''Examples:'''
<source lang="html4strict">
<!-- // Adding taglibs to your template //-->
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addcss" prefix="htmlheader" name="addcss" />
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addjs" prefix="htmlheader" name="addjs" />
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addtitle" prefix="htmlheader" name="addtitle" />
<core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addpackage" prefix="htmlheader" name="addpackage" />

<!-- // Using taglibs //-->
<htmlheader:addjs namespace="sites::example::pres::frontend::static::js" filename="jquery.min" />
<htmlheader:addcss namespace="sites::example::pres::frontend::static::css" filename="stylesheet" />
<htmlheader:addtitle append="false">This is an example title</htmlheader:addtitle>

<!-- // Using external file support, added in 1.12 //-->
<htmlheader:addjs
    url="http://static/"
    folder="js::anything"
    filename="jquery.min"
    rewriting="false"
    fcaction="false"
/>

<htmlheader:addcss
    url="http://static/"
    folder="css::anything"
    filename="stylesheet"
    rewriting="false"
    fcaction="false"
/>

<!-- // Using JsCssPackager, an extension added in 1.12 //-->
<htmlheader:addpackage
    name="form_clientvalidators_all"
    type="js"
/>
<htmlheader:addpackage
    name="mystylesheetpackage"
    type="css"
/>
</source>


'''The parameters:'''
* url: If you want to use an external file, set the url of target server.
* folder: If you use an external file, specify folder which contains the file.
* namespace: The namespace of the file.
* filename: The name of the file, without extension (e.g. '.css', '.js').
* rewriting: Optional. Generate url which uses url-rewriting? (Default: Same as application)
* fcaction: Optional Use fc action for delivering file? (Default: true)
* append: Set append to true if you want to add the given title at the end of the already existing title. Otherwise set to false, if you want to overwrite existing titles.

Using without other taglibs, e.g. in document controller:
You need to place the gethead taglib, but for the other taglibs you can use the
following. (And for meta-refreshs you need to use it)

<source lang="php">
// Get an instance of HtmlHeaderManager:
$HHM = $this->getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');

// Add a refresh on index.php?test=abc, with a delay of 5 seconds:
$HHM->addRefresh('index.php', 5, array("test" => "abc"));

// Add a title (direct edit the $title variable of HHM)
$HHM->title = "Example title";

// Import css-node class
import('extensions::htmlheader::biz','CssNode');

// Get instance and configure in constructor: (Before 1.12 Revision 874)
$CssNode = new CssNode($namespace, $filename);
//Get instance and configure in constructor: (After 1.12 Revision 874)
//$CssNode = new CssNode($url, $namespace, $filename, $rewriting, $fcaction);

// Add Node to HHM
$HHM->addCss($CssNode);

// Import js-node class
import('extensions::htmlheader::biz','JsNode');

// Get instance and configure in constructor: (Before 1.12 Revision 874)
$JsNode = new JsNode($namespace, $filename);
// Get instance and configure in constructor: (After 1.12 Revision 874)
//$JsNode = new JsNode($url, $namespace, $filename, $rewriting, $fcaction);

// Add Node to HHM
$HHM->addJs($JsNode);


// Define and add a JsCssPackager-Package to HHM
$PackageNode = new PackageNode($url, $name, $type, $rewriting);
$HHM->addPackage($PackageNode);
</source>

That's all you need to know.
Have fun.
