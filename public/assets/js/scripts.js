// div#top-nav
let menuIcon = document.getElementById('nav-icon'); // div#nav-icon [hamburger and close icon, svg]
let topNav = document.getElementById('top-nav');
// when hamburger icon is clicked
menuIcon.addEventListener('click', function(){
    // div#top-nav add class .nav-active
    topNav.classList.toggle('nav-active');
    // change menu icon from menu-ham-black to menu-close-black.svg (in CSS background)
    // menuIcon.classList.toggle('menu-close');
});
// div#top-nav
let sousMenuIcon = document.getElementById('toggle-compte');
let sousMenu = document.getElementById('sous-menu');
// when hamburger icon is clicked
sousMenuIcon.addEventListener('click', function(){
    // div#top-nav add class .nav-active
    sousMenu.classList.toggle('sous-menu');
    // change menu icon from menu-ham-black to menu-close-black.svg (in CSS background)
    // menuIcon.classList.toggle('menu-close');
});

// Datepicker en deux champs avec heure
let input = document.getElementById('litepicker')
let picker = new Litepicker({
    element: document.getElementById('start-date'),
    elementEnd: document.getElementById('end-date'),
    singleMode: false,
    format:'DD/MM/YYYY',
    lang:'fr-FR',
    splitView:true,
    mobileFriendly:true,
})

// gallerie
// var elem = document.querySelector('.carousel');
// var flkty = new Flickity( elem, {
//   // options
//   wrapAround: true,
// });

// Plan Mapbox
// mapboxgl.accessToken = 'pk.eyJ1IjoiY2Vzbm8iLCJhIjoielgwaWt4RSJ9.7AIU4sSCwzmunrIXetc1DQ;
// var map = new mapboxgl.Map({
// container: 'map', // container id
// style: 'mapbox://styles/cesno/ckes74kzs7th31at25rxa26ru', // style URL
// center: [-74.5, 40], // starting position [lng, lat]
// zoom: 9 // starting zoom
// });