<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">        
        <title>Test</title>
        <link rel="stylesheet" href="css/app.css">
        
        <!-- csrf対策でトークンを付ける -->
        <script>
            window.Laravel = {};
            window.Laravel.csrfToken = "{{ csrf_token() }}";
        </script>
    </head>
    <body oncontextmenu="return false;">
        <div id="container">
            <div id="app">
                <!-- ここにvue.jsのコンポーネントが表示されたり -->
                <test></test>
            </div>
            <div id="footer">
                from <a href="https://qiita.com/t_mitarai/private/4b5ccb2cc10dfb04ed7f">Laravel + Vue.js + Three.js でポリゴンを表示してみよう</a>
            </div>
        </div>
    </body>
    <!--コンパイルしたjsの読み込み-->
    <script src="js/app.js"></script>
</html>
