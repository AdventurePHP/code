<?php
import('extensions::htmlheader::biz','PackageNode');
/**
 * Description of htmlheader_taglib_addpackage
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  20.03.2010<br />
 */
class htmlheader_taglib_addpackage extends Document {

    public function onParseTime() {
        $HHM = $this->__getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');

        $url = $this->getAttribute('url');
        $name = $this->getAttribute('name');
        $type = $this->getAttribute('type');
        $rewriting = $this->getAttribute('rewriting');

        if($rewriting === "true") {
            $rewriting = true;
        }
        elseif($rewriting === "false") {
            $rewriting = false;
        }
        $PackageNode = new PackageNode($url, $name, $type, $rewriting);
        $HHM->addPackage($PackageNode);
    }
    public function transform() {
    }
}
?>
