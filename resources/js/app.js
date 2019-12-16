require('./bootstrap');

// コンポーネント読み込み
// .default は、あとでコンポーネントを export defaultで宣言している関係で必要
Vue.component('test', require('./components/test.vue').default);

// Vueがルートとして使う要素を指定。
// test.blade.phpのid="app"が記述されたdivの部分が相当。
const app = new Vue({
    el: '#app'
});
