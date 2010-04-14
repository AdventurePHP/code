Die formatierte Version kann unter http://wiki.adventure-php-framework.org/de/Javascript-_und_Css-Paketmanager betrachtet werden.

= JsCssPackager =

== Funktion ==
Der JsCssPackager wurde von [[Benutzer:Screeze|Ralf Schubert]] entworfen um eine Steigerung der Performance bei Verwendung von vielen Javascript und Css Dateien zu erreichen.
Bei den meisten Projekten müssen mehrere JS und CSS Dateien ausgeliefert werden. Jedes Ausliefern benötigt Performance und Bandbreite. Hier hilft der JsCssPackager folgendermaßen:

* Es wird nurnoch '''eine''' JS und '''eine''' CSS datei pro paket ausgeliefert.
* Ein Paket kann beliebig viele Dateien enthalten
* Die Pakete werden (wenn gewünscht) durch ein Shrinking-Tool geschickt, welches die Dateigröße verkleinert.
* Die Pakete werden (wenn gewünscht) per GZIP gepackt vor Auslieferung.
* Die fertigen Pakete können (wenn gewünscht) Server- und Clientseitig für einen seperat definierbaren Zeitraum gecached werden (gepackt und ungepackt, falls ein client keine gzip komprimierung unterstützt).



== Konfiguration ==
Der JsCssPackager benötigt 2 Konfigurationsdateien:

* /config/extensions/jscsspackager/biz/actions/{CONTEXT}/{ENVIRONMENT}_actionconfig.ini
<source lang="php">
[jcp]
FC.ActionNamespace = "extensions::jscsspackager::biz::actions"
FC.ActionFile = "JsCssPackagerAction"
FC.ActionClass = "JsCssPackagerAction"
FC.InputFile = "JsCssPackagerInput"
FC.InputClass = "JsCssPackagerInput"
FC.InputParams = ""
</source>

* /config/extensions/jscsspackager/biz/{CONTEXT}/{ENVIRONMENT}_JsCssPackager.ini
<source lang="php">
; create one section for each package, like the one below
[form_clientvalidators_all]
ClientCacheDays = "7"           ; How long should it be cached on client?
ServerCacheMinutes = "10080"    ; How long should it be cached on server?
EnableShrinking = "true"        ; Shrink the code in the package?
PackageType = "js"              ; Possible types: 'js' and 'css'
; The files the package contains. Filename without extension!
Files.1.Namespace = "extensions::form::client::validator"
Files.1.Filename = "FormValidator"
Files.2.Namespace = "extensions::form::client::validator"
Files.2.Filename = "EMailValidator"
Files.3.Namespace = "extensions::form::client::validator"
Files.3.Filename = "FieldCompareValidator"
[...]
</source>

In dieser Konfigurationsdatei werden die Pakete definiert.
Jedes Paket bekommt eine eigene Sektion: [Paketname]

Darunter wird das Paket wie oben zu sehen konfiguriert.
* ClientCacheDays: Wie viele Tage das Paket beim client gecached werden soll. (0 für kein caching)
* ClientCacheMinutes: Wie viele Minuten das fertig generierte Paket auf dem server gecached werden soll. (0 für kein caching)
* EnableShrinking: Soll der Code im Paket vor Auslieferung (bzw. vor dem serverseitigen Cachen) verkleinert werden?
* PackageType: Handelt es sich um ein '''css''' oder '''js''' Paket?

Danach werden die Dateien, welche im Paket enthalten sein sollen definiert.
Jede Datei wird als eine Zahl (numerischer Subsection-Index) definiert. Hier werden folgende Angaben benötigt:
* Namespace: Unter welchem Namespace liegt die Datei?
* Filename: Wie ist der Dateiname (ohne Endung)?



== Verwendung ==
Das Paket wird einfach durch die Html Tags eingebunden:
<source lang="html4strict">
<script src="{URL}" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="{URL}" />
</source>

Die {URL} setzt sich wie folgt zusammen, wobei ''{PAKETNAME}'' ersetzt werden muss:

'''Aktives URL-Rewriting:'''

server.de/~/extensions_jscsspackager_biz-action/jcp/package/''{PAKETNAME}''.js

server.de/~/extensions_jscsspackager_biz-action/jcp/package/''{PAKETNAME}''.css


'''Ohne URL-Rewriting:'''

index.php?extensions_jscsspackager_biz-action:jcp=package:''{PAKETNAME}''.js

index.php?extensions_jscsspackager_biz-action:jcp=package:''{PAKETNAME}''.css


''Tipp: Die Extension [[Html-Header_Erweiterung|HtmlHeader]] unterstützt das dynamische Einbinden eines Pakets des JsCssPackagers an einer beliebigen Position im Code und übernimmt die Generierung der URL.''


