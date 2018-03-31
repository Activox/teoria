<!DOCTYPE>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>

    <link type="text/css" rel="stylesheet" href="intro/introjs.css" />
    <link type="text/css" rel="stylesheet" href="intro/themes/introjs-flattener.css" />

</head>
<body style="padding: 20px;">

    <div id="test">
        <p>Esto es una prueba</p>
    </div>

    <ul id="lista">
        <li id="steps1">step 1</li>
        <li id="steps2">step 2</li>
        <li id="steps3">step 3</li>
        <li id="steps4">step 4</li>
        <li id="steps5">step 5</li>
    </ul>

    <button onclick="javascript:basic();">SHOW ONE</button>

    <button onclick="javascript:withFile();">SHOW ONE FROM FILE</button>

    <button onclick="javascript:testMultiDocByJson();">MULTIPLE</button>

    <button onclick="javascript:testMultiDoc();">MULTIPLE FROM FILE</button>

    <button onclick="javascript:hint();">SHOW HINT</button>

    <script src="intro/intro.js"></script>
    <script src="intro/Documentation.js"></script>

    <script>

        /*for multiple*/
        function testMultiDoc(){
            var doc = new Documentation();
            doc.docsFromJson("test.php", "", true);
        }
        /*for multiple by json*/
        function testMultiDocByJson(){
            var json = [
                {
                    id : "steps1",
                    docs : "Hola mundo",
                    position : "left"
                },
                {
                    id : "steps2",
                    docs : "Hola mundo",
                    position : "left"
                },
                {
                    id : "steps3",
                    docs : "Hola mundo",
                    position : "left"
                },
                {
                    id : "steps4",
                    docs : "Hola mundo",
                    position : "left"
                },
                {
                    id : "steps5",
                    docs : "Hola mundo",
                    position : "left"
                }
            ];
            var doc = new Documentation();
            doc.setJson(json);
            doc.multipleDocs();
        }
        /*basic with file path*/
        function withFile(){
            var doc = new Documentation();
            doc.docsFromJson("", "http://127.0.0.1/test/intro/jsonFiles/test.json", false);
            introJs().start();
        }
        /*basic with json variable*/
        function basic() {
            var json = [{
                id : "test",
                docs : "Hola mundo"
            }];
            var documentation = new Documentation();
            documentation.setJson(json);
            documentation.setDocs();
            introJs().start();
        }
        /*basic hint*/
        function hint() {
            var json = [{
                id : "test",
                docs : "Hola mundo"
            }];
            var documentation = new Documentation();
            documentation.setJson(json);
            documentation.setHint();
            introJs().addHints();
        }

    </script>

</body>
</html>