<template>
    <div class="content">
        <h1>Merry X'mas !!</h1>
        <!--idではなくrefで指定-->
        <div ref="myCanvas" style="width:320px;height:240px;background-color:#08cc08;"></div>
    </div>
</template>

<script>
import * as THREE from 'three'; // three.jsをimport

/* ここに描画処理などを実装していきます */
export default {
    name: 'test',

    data () {
        // シーン
        let scene = new THREE.Scene();

        // 物体
        let geometryBox = new THREE.BoxGeometry (2, 2, 2);
        let geometryBoxTop = new THREE.BoxGeometry (2.1, 0.8, 2.1);
        let geometryRibbon1 = new THREE.BoxGeometry (0.6, 2.11, 2.11);
        let geometryRibbon2 = new THREE.BoxGeometry (2.11, 2.11, 0.6);

        let geometryRibbonTop1 = new THREE.BoxGeometry (0.7, 0.1, 0.7);
        let geometryRibbonTop2 = new THREE.BoxGeometry (0.7, 0.1, 0.7);
        let geometryRibbonTop3 = new THREE.BoxGeometry (0.7, 0.1, 0.7);
        let geometryRibbonTop4 = new THREE.BoxGeometry (0.7, 0.1, 0.7);

        // 色
        let material1 = new THREE.MeshLambertMaterial ({ color: 0xff5555 });
        let material2 = new THREE.MeshStandardMaterial ({ color: 0xffff33 });

        // メッシュ
        let box = new THREE.Mesh (geometryBox, material1);
        let boxTop = new THREE.Mesh (geometryBoxTop, material1);
        let ribbon1 = new THREE.Mesh (geometryRibbon1, material2);
        let ribbon2 = new THREE.Mesh (geometryRibbon2, material2);
        let ribbonTop1 = new THREE.Mesh (geometryRibbonTop1, material2);
        let ribbonTop2 = new THREE.Mesh (geometryRibbonTop2, material2);
        let ribbonTop3 = new THREE.Mesh (geometryRibbonTop3, material2);
        let ribbonTop4 = new THREE.Mesh (geometryRibbonTop4, material2);

        boxTop.position.set(0, 0.65, 0);
        ribbonTop1.position.set(0.3, 1.1, 0);
        ribbonTop1.rotation.x -= Math.PI/4;
        ribbonTop1.rotation.y += Math.PI/8;
        ribbonTop2.position.set(0, 1.1, 0.3);
        ribbonTop2.rotation.x -= Math.PI/8;
        ribbonTop2.rotation.y -= Math.PI/4;
        ribbonTop3.position.set(-0.3, 1.1, 0);
        ribbonTop3.rotation.x += Math.PI/4;
        ribbonTop3.rotation.y += Math.PI/8;
        ribbonTop4.position.set(0, 1.1, -0.3);
        ribbonTop4.rotation.x += Math.PI/8;
        ribbonTop4.rotation.z -= Math.PI/4;

        let presentBox = new THREE.Group();
        presentBox.add(box);
        presentBox.add(boxTop);
        presentBox.add(ribbon1);
        presentBox.add(ribbon2);
        presentBox.add(ribbonTop1);
        presentBox.add(ribbonTop2);
        presentBox.add(ribbonTop3);
        presentBox.add(ribbonTop4);

        // 光源
        let light = new THREE.DirectionalLight(0xffffff);

        // カメラ
        let camera = new THREE.PerspectiveCamera();

        // レンダラー
        let renderer = new THREE.WebGLRenderer ({antialias : true, alpha: true});

        return {            
            scene: scene,
            presentBox: presentBox,
            light: light,
            camera: camera,
            renderer: renderer,
        };
    },
    created () {        
        // 箱の位置設定
        this.presentBox.position.set(0, 0, 0);

        // 光源位置設定
        this.light.position.set(3, 3, 3);

        // カメラの位置を設定
        this.camera.position.set(1, 4, 5);

        // カメラの向き先を設定
        this.camera.lookAt(new THREE.Vector3(0, 0, 0));

        // シーンに配置
        this.scene.add( this.presentBox );
        this.scene.add( this.light);
        this.scene.add( this.camera );

        // レンダラー設定
        this.renderer.setPixelRatio( window.devicePixelRatio );
        this.renderer.autoClear = false;
    },
    mounted () {
        // 要素へのマウントがされた後に実行される処理を書きます。

        // 描画領域サイズ
        const width  = 320;
        const height = 240;

        // カメラ設定 
        const fov     = 45;           // 画角
        const aspect  = width/height; // アスペクト比
        const near    = 0.1;          // 最低描画距離
        const far     = 10000;        // 最大描画距離

        this.scene.fog = new THREE.Fog( 0xddeeff, 0, 750 );   // 空気遠近

        this.renderer.setSize( width, height );

        // カメラ設定 
        this.camera.fov    = fov;
        this.camera.aspect = width / height;
        this.camera.near   = near;
        this.camera.far    = far;

        // カメラ更新
        this.camera.updateProjectionMatrix();

        // 描画領域を実装
        this.$refs.myCanvas.appendChild( this.renderer.domElement );

        this.animate();
    },
    methods: {
        // メソッドを実装します。
        animate () {
            requestAnimationFrame( this.animate );

            //this.presentBox.rotation.x += 0.01;
            this.presentBox.rotation.y += 0.01;

            this.renderer.clear();
            this.renderer.depthTest = true;
            this.renderer.depthWrite = true;
            this.renderer.render( this.scene, this.camera );
        }
    }
}
</script>
