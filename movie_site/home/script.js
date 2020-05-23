'use strict';
{
//場所の取得
  const open = document.getElementById('open');
  const close = document.getElementById('close');
  const modal = document.getElementById('modal');
  const mask = document.getElementById('mask');

  const open2 = document.getElementById('open2');
  const close2 = document.getElementById('close2');
  const modal2 = document.getElementById('modal2');
  const mask2 = document.getElementById('mask2');

  // const open3 = document.getElementById('open3');
  // const close3 = document.getElementById('close3');
  // const modal3 = document.getElementById('modal3');
  // const mask3 = document.getElementById('mask3');


//#openが押されたら#modelと#maskのhiddenを取り除く(表示させる)
//今回はdisplay:none;としていたhidden_classを取り除くことでcssを機能させなくする
  open.addEventListener('click', function () {
    modal.classList.remove('hidden');
    mask.classList.remove('hidden');
  });
//押したらdisplay:hidden;を持つclassを追加するだけで消せる
  close.addEventListener('click', function () {
    modal.classList.add('hidden');
    mask.classList.add('hidden');
  });
  
  mask.addEventListener('click', function () {
    modal.classList.add('hidden');
    mask.classList.add('hidden');
  });

// #open2ヴァージョン
  open2.addEventListener('click', function () {
    modal2.classList.remove('hidden');
    mask2.classList.remove('hidden');
  });
  close2.addEventListener('click', function () {
    modal2.classList.add('hidden');
    mask2.classList.add('hidden');
  });
  mask2.addEventListener('click', function () {
    modal2.classList.add('hidden');
    mask2.classList.add('hidden');
  });
}
//3
//   open3.addEventListener('click', function () {
//     modal3.classList.remove('hidden');
//     mask3.classList.remove('hidden');
//   });
//   close3.addEventListener('click', function () {
//     modal3.classList.add('hidden');
//     mask3.classList.add('hidden');
//   });
//   mask3.addEventListener('click', function () {
//     modal3.classList.add('hidden');
//     mask3.classList.add('hidden');
//   });
// }
