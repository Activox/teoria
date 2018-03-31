function Documentation ( ) { this.docs = null; this.path = "lib/vendor/documentation/intro/jsonFiles/";}

function Docs () { this.json = null; this.introSteps = introJs(); }

Documentation.prototype = {
    /**
     * set Json variable
     * @param json json
     */
    setJson : function ( json ) {
        this.docs = new Docs();
        this.docs.setJson( json );
    },
    /**
     * put documentation by json
     */
    setDocs : function (){
        this.docs.setDocs();
    },
    /**
     *  put documentation from json file
     * @param jsonFileName
     */
    docsFromJson : function ( jsonFileName, jsonPath, multiple ) {

        multiple = ( multiple != undefined ) ? multiple : false;

        if( ( jsonFileName == undefined && jsonPath == undefined ) || ( jsonFileName.length < 1 && jsonPath.length < 1 ) ) {
            alert( "We could not find the json file" );
            return false;
        }

        this.path = ( jsonPath != undefined && jsonPath.length > 0 ) ? jsonPath : this.path + jsonFileName;

        var rawFile = new XMLHttpRequest();

        rawFile.overrideMimeType("application/json");
        rawFile.open("GET", this.path, true);
        rawFile.send();
        rawFile.onreadystatechange = function() {

            if (rawFile.readyState === 4 && rawFile.status == "200") {

                var
                    json = JSON.parse(rawFile.responseText),
                    docs = new Docs();

                if (!multiple) {
                    docs.setJson(json);
                    docs.setDocs();
                } else {
                    docs.setJson(json);
                    docs.setMultiDocs();
                    docs.launchMultiDocs();
                }
            }

        }
    },
    /**
     * multiple docs by json
     */
    multipleDocs : function () {
        this.docs.setMultiDocs();
        this.docs.launchMultiDocs();
    },
    /**
     * put hint
     */
    setHint : function () {
        this.docs.setHint();
    }

}

Docs.prototype = {
    /**
     * set Json variable
     * @param json json
     */
    setJson : function ( json ) {
    this.json = json;
},
    /**
     * put documentation by json
     */
    setDocs : function () {

        var arr = this.json,
            element = null
        ;

        for ( var i = 0; i < arr.length; i++ ) {

            element = arr[i];

            if( element.id != undefined && element.id.length > 0 ) {
                document.getElementById( element.id ).setAttribute("data-intro", element.docs);
            }
            else
            {
                if( element.class != undefined && element.class.length > 0 ) {

                    var ec = document.getElementsByClassName( element.class ),
                        li = null;

                    for ( var i = 0; i < ec.length; i++ ) {
                        li = ec[i];
                        li.setAttribute("data-intro", element.docs);
                    }

                }
            }

        }

    },
    /**
     * put multiple documentation by json
     */
    setMultiDocs : function ( ) {

        var arr = this.json,

            steps = [],

            element = null
        ;

        for ( var i = 0; i < arr.length; i++ ) {

            element = arr[i];

            if( element.id != undefined && element.id.length > 0 ) {

                steps.push({
                    element: document.getElementById( element.id ),
                    intro: element.docs,
                    position: element.position
                });

            }

        }

        this.introSteps.setOptions({steps: steps});

    },
    /**
     * launch multiple documentation
     */
    launchMultiDocs : function () {
        // console.log(this.introSteps);
        this.introSteps.start();
    },
    /**
     * put hint
     */
    setHint : function () {

        var arr = this.json,
            element = null
        ;

        for ( var i = 0; i < arr.length; i++ ) {

            element = arr[i];

            if( element.id != undefined && element.id.length > 0 ) {
                document.getElementById( element.id ).setAttribute("data-hint", element.docs);
            }
            else
            {
                if( element.class != undefined && element.class.length > 0 ) {

                    var ec = document.getElementsByClassName( element.class ),
                        li = null;

                    for ( var i = 0; i < ec.length; i++ ) {
                        li = ec[i];
                        li.setAttribute("data-hint", element.docs);
                    }

                }
            }

        }

    }

}