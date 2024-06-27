import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
// import 'bootstrap/dist/css/bootstrap.min.css';
import 'mdb-ui-kit/css/mdb.min.css';
import { Dropdown, Input, Ripple  , initMDB } from 'mdb-ui-kit';
initMDB({ Dropdown, Input, Ripple });
import autocomplete from 'autocompleter';
import levenshtein from 'js-levenshtein-esm';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// Mutation Observer
// Select the node that will be observed for mutations
const targetNode = document.getElementById("search_champions");
// Options for the observer (which mutations to observe)
const config = { attributes: false, childList: true, subtree: true };
// Callback function to execute when mutations are observed
const callback = (mutationList, observer) => {
    [... document.querySelectorAll('[data-mdb-input-init]:not([data-mdb-input-initialized])')].forEach(element => {
        new Input(element).init();
    })
};
// Create an observer instance linked to the callback function
const observer = new MutationObserver(callback);
// Start observing the target node for configured mutations
observer.observe(targetNode, config);
// Later, you can stop observing
// observer.disconnect();


// Form movements :
const form = document.getElementsByName('search')[0];
const formLargePosition = document.getElementById('form-position-large');
const formSmallPosition = document.getElementById('form-position-small');
var view = '';
function moveForm() {
    const windowWidth = window.innerWidth;
    if(windowWidth >= 1200) {
        formLargePosition.appendChild(form);
        view = 'large';
    } else {
        formSmallPosition.appendChild(form);
        view = 'small';
    }
}
// Move form on resize :
window.addEventListener('resize', moveForm);
// Move the form when the page is init :
moveForm();

// Disable button ADD on page refresh :
const championElement = document.getElementById('search_champions_9_champion');
if (championElement) {
    const button = document.getElementById('add-button');
    button.setAttribute('disabled', '');
}

// Event when add button is clicked (fix dropdown closing)
const addButton = document.getElementById('add-button');
addButton.addEventListener('click', function (event){
    event.stopPropagation();

    triggerAutocomplete();
});

// autocompleter
const champions = [{"label":"Aatrox","value":"Aatrox"},{"label":"Ahri","value":"Ahri"},{"label":"Akali","value":"Akali"},{"label":"Akshan","value":"Akshan"},{"label":"Alistar","value":"Alistar"},{"label":"Amumu","value":"Amumu"},{"label":"Anivia","value":"Anivia"},{"label":"Annie","value":"Annie"},{"label":"Aphelios","value":"Aphelios"},{"label":"Ashe","value":"Ashe"},{"label":"Aurelion Sol","value":"Aurelion Sol"},{"label":"Azir","value":"Azir"},{"label":"Bard","value":"Bard"},{"label":"Bel'Veth","value":"Bel'Veth"},{"label":"Blitzcrank","value":"Blitzcrank"},{"label":"Brand","value":"Brand"},{"label":"Braum","value":"Braum"},{"label":"Briar","value":"Briar"},{"label":"Caitlyn","value":"Caitlyn"},{"label":"Camille","value":"Camille"},{"label":"Cassiopeia","value":"Cassiopeia"},{"label":"Cho'Gath","value":"Cho'Gath"},{"label":"Corki","value":"Corki"},{"label":"Darius","value":"Darius"},{"label":"Diana","value":"Diana"},{"label":"Dr. Mundo","value":"Dr. Mundo"},{"label":"Draven","value":"Draven"},{"label":"Ekko","value":"Ekko"},{"label":"Elise","value":"Elise"},{"label":"Evelynn","value":"Evelynn"},{"label":"Ezreal","value":"Ezreal"},{"label":"Fiddlesticks","value":"Fiddlesticks"},{"label":"Fiora","value":"Fiora"},{"label":"Fizz","value":"Fizz"},{"label":"Galio","value":"Galio"},{"label":"Gangplank","value":"Gangplank"},{"label":"Garen","value":"Garen"},{"label":"Gnar","value":"Gnar"},{"label":"Gragas","value":"Gragas"},{"label":"Graves","value":"Graves"},{"label":"Gwen","value":"Gwen"},{"label":"Hecarim","value":"Hecarim"},{"label":"Heimerdinger","value":"Heimerdinger"},{"label":"Hwei","value":"Hwei"},{"label":"Illaoi","value":"Illaoi"},{"label":"Irelia","value":"Irelia"},{"label":"Ivern","value":"Ivern"},{"label":"Janna","value":"Janna"},{"label":"Jarvan IV","value":"Jarvan IV"},{"label":"Jax","value":"Jax"},{"label":"Jayce","value":"Jayce"},{"label":"Jhin","value":"Jhin"},{"label":"Jinx","value":"Jinx"},{"label":"K'Sante","value":"K'Sante"},{"label":"Kai'Sa","value":"Kai'Sa"},{"label":"Kalista","value":"Kalista"},{"label":"Karma","value":"Karma"},{"label":"Karthus","value":"Karthus"},{"label":"Kassadin","value":"Kassadin"},{"label":"Katarina","value":"Katarina"},{"label":"Kayle","value":"Kayle"},{"label":"Kayn","value":"Kayn"},{"label":"Kennen","value":"Kennen"},{"label":"Kha'Zix","value":"Kha'Zix"},{"label":"Kindred","value":"Kindred"},{"label":"Kled","value":"Kled"},{"label":"Kog'Maw","value":"Kog'Maw"},{"label":"LeBlanc","value":"LeBlanc"},{"label":"Lee Sin","value":"Lee Sin"},{"label":"Leona","value":"Leona"},{"label":"Lillia","value":"Lillia"},{"label":"Lissandra","value":"Lissandra"},{"label":"Lucian","value":"Lucian"},{"label":"Lulu","value":"Lulu"},{"label":"Lux","value":"Lux"},{"label":"Malphite","value":"Malphite"},{"label":"Malzahar","value":"Malzahar"},{"label":"Maokai","value":"Maokai"},{"label":"Master Yi","value":"Master Yi"},{"label":"Milio","value":"Milio"},{"label":"Miss Fortune","value":"Miss Fortune"},{"label":"Mordekaiser","value":"Mordekaiser"},{"label":"Morgana","value":"Morgana"},{"label":"Naafiri","value":"Naafiri"},{"label":"Nami","value":"Nami"},{"label":"Nasus","value":"Nasus"},{"label":"Nautilus","value":"Nautilus"},{"label":"Neeko","value":"Neeko"},{"label":"Nidalee","value":"Nidalee"},{"label":"Nilah","value":"Nilah"},{"label":"Nocturne","value":"Nocturne"},{"label":"Nunu & Willump","value":"Nunu & Willump"},{"label":"Olaf","value":"Olaf"},{"label":"Orianna","value":"Orianna"},{"label":"Ornn","value":"Ornn"},{"label":"Pantheon","value":"Pantheon"},{"label":"Poppy","value":"Poppy"},{"label":"Pyke","value":"Pyke"},{"label":"Qiyana","value":"Qiyana"},{"label":"Quinn","value":"Quinn"},{"label":"Rakan","value":"Rakan"},{"label":"Rammus","value":"Rammus"},{"label":"Rek'Sai","value":"Rek'Sai"},{"label":"Rell","value":"Rell"},{"label":"Renata Glasc","value":"Renata Glasc"},{"label":"Renekton","value":"Renekton"},{"label":"Rengar","value":"Rengar"},{"label":"Riven","value":"Riven"},{"label":"Rumble","value":"Rumble"},{"label":"Ryze","value":"Ryze"},{"label":"Samira","value":"Samira"},{"label":"Sejuani","value":"Sejuani"},{"label":"Senna","value":"Senna"},{"label":"Seraphine","value":"Seraphine"},{"label":"Sett","value":"Sett"},{"label":"Shaco","value":"Shaco"},{"label":"Shen","value":"Shen"},{"label":"Shyvana","value":"Shyvana"},{"label":"Singed","value":"Singed"},{"label":"Sion","value":"Sion"},{"label":"Sivir","value":"Sivir"},{"label":"Skarner","value":"Skarner"},{"label":"Smolder","value":"Smolder"},{"label":"Sona","value":"Sona"},{"label":"Soraka","value":"Soraka"},{"label":"Swain","value":"Swain"},{"label":"Sylas","value":"Sylas"},{"label":"Syndra","value":"Syndra"},{"label":"Tahm Kench","value":"Tahm Kench"},{"label":"Taliyah","value":"Taliyah"},{"label":"Talon","value":"Talon"},{"label":"Taric","value":"Taric"},{"label":"Teemo","value":"Teemo"},{"label":"Thresh","value":"Thresh"},{"label":"Tristana","value":"Tristana"},{"label":"Trundle","value":"Trundle"},{"label":"Tryndamere","value":"Tryndamere"},{"label":"Twisted Fate","value":"Twisted Fate"},{"label":"Twitch","value":"Twitch"},{"label":"Udyr","value":"Udyr"},{"label":"Urgot","value":"Urgot"},{"label":"Varus","value":"Varus"},{"label":"Vayne","value":"Vayne"},{"label":"Veigar","value":"Veigar"},{"label":"Vel'Koz","value":"Vel'Koz"},{"label":"Vex","value":"Vex"},{"label":"Vi","value":"Vi"},{"label":"Viego","value":"Viego"},{"label":"Viktor","value":"Viktor"},{"label":"Vladimir","value":"Vladimir"},{"label":"Volibear","value":"Volibear"},{"label":"Warwick","value":"Warwick"},{"label":"Wukong","value":"Wukong"},{"label":"Xayah","value":"Xayah"},{"label":"Xerath","value":"Xerath"},{"label":"Xin Zhao","value":"Xin Zhao"},{"label":"Yasuo","value":"Yasuo"},{"label":"Yone","value":"Yone"},{"label":"Yorick","value":"Yorick"},{"label":"Yuumi","value":"Yuumi"},{"label":"Zac","value":"Zac"},{"label":"Zed","value":"Zed"},{"label":"Zeri","value":"Zeri"},{"label":"Ziggs","value":"Ziggs"},{"label":"Zilean","value":"Zilean"},{"label":"Zoe","value":"Zoe"},{"label":"Zyra","value":"Zyra"}]

champions.forEach(champion => {
    champion.label_sanitized = sanitize(champion.label);
});
function sanitize(text){
    return text.replace(/[-']/g, '').toLowerCase();
}
function autocompletion(id) {
    let input = document.getElementById(id);
    if (input) {
        autocomplete({
            input: input,
            minLength: 2,
            fetch: function(text, update) {
                text = sanitize(text);
                let score = {};

                champions.forEach(champion => {
                    const lev = levenshtein(champion.label_sanitized, text);
                    if(lev <= (text.length / 3) || champion.label_sanitized.includes(text)){
                        score[champion.label] = lev
                    }
                });

                const entries = Object.entries(score);
                entries.sort((a,b) => a[0].localeCompare(b[0]));
                let suggestions = champions.filter(champion => entries.some(entry => entry[0] === champion.label))
                update(suggestions);
            },
            onSelect: function(item) {
                input.value = item.label;
            },
            render: function(item, currentValue) {
                let div = document.createElement('div');
                div.textContent = item.label;
                return div;
            },
            className: 'mx-1 p-1 bg-dark-subtle text-white-50',
            customize: function(input, inputRect, container, maxHeight) {
                if (view === 'small') {
                container.style.left = '350px';
                }
            }
        });
    }
}

function triggerAutocomplete() {
    setTimeout(function() {
        for (let i = 0; i < 10;  i++ ){
            autocompletion('search_champions_' + i + '_champion')
        }
    }, 500);
}

triggerAutocomplete();




