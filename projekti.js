
var Project = (function () {

    /**
     *  -projectTable je bljižnjica za element table-body
     */
    //var projectTable = document.querySelector('#projects-table');
        //newButton    = document.querySelector('.button.new');

    /**
     * Make APi calls to backend
     * @param {string} url 
     * @param {string} method  "POST|GET"
     * @param {object} data 
     * @param {function} callback 
     */
    function ajaxCall (url, method, data, callback) {

        /* PARAM CHECKERS
        if (!url) { url = 'index.php'; }

        if (!method) { method = 'GET'; }

        if (!data) { data = ''; }

        if (!callback) { callback = function (data) {
                console.log("Manjkajoč callback!");
                console.log(data);
            }
        }
        */

        var xhttp = new XMLHttpRequest();
            post  = JSON.stringify(data);
        
        console.log("JS request: " + post)

        if (typeof callback == 'function') {
            xhttp.onreadystatechange = function () {
                if(this.readyState == 4 && this.status == 200)
                {
                    try{
                        console.log(xhttp.responseText);
                        var response = JSON.parse(xhttp.responseText);
                        //console.log(response);
                    } catch(error){
                        console.log(error.message + " in " + xhttp.responseText);
                        return;
                    }

                    if (response['success'] == true){
                        callback(response['data']);
                    } else {
                        alert("Ajax call unsuccessfull");
                    }
                }
            }
        };

        xhttp.open(method.toUpperCase(), url, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(post); //typeof data == 'string' ? data :JSON.stringify(data)
    }

    /**
     * Create table row for project
     * @param {string} text 
     * @param {function} klik
     * @param {boolean} head 
     */
    function makeTD (text, klik, head) {
        if (head == true) {
            var tableRow = document.createElement('th');
            tableRow.setAttribute('scope', 'row');
        } else {
            var tableRow = document.createElement('td');
        }
        tableRow.setAttribute('style', 'text-align: center; ')
        tableRow.innerHTML = text;

        if (typeof klik == 'function') {
            tableRow.addEventListener('click', klik);
        }

        return tableRow;
        /*var projectId = row['id'];
            document.getElementById(projectId).addEventListener('click', function () { 
                //console.log("gumb kliknjen" + projectId);
                Project.openProject(projectId);
            });
        */
    }

    /**
     * Create table of projects and append
     * @param {object} data 
     */
    function addProjectRows (data) {
        
        //var html="";
        //INICIALIZIRAM IFRAME LOKACIJO TABELE
        var iframe = document.getElementById('iframe-table');
        var table  = iframe.contentWindow.document.getElementById('table2');

        data.forEach( function(row)
        {
            //console.log(row);

            var tableRow = document.createElement('tr');

            tableRow.setAttribute('class', 'project');
            tableRow.appendChild(makeTD(row['ime'], function () { 
                Project.openProject(row['id']); 
            }, true));

            tableRow.appendChild(makeTD(row['material']));
            tableRow.appendChild(makeTD(row['format']));
            tableRow.appendChild(makeTD(row['oglasevalec']));

            //projectTable.appendChild(tableRow);
            table.appendChild(tableRow);

        });
        // dodaj vrstice v tabelo
        //projectTable.innerHTML = html;
    }


    //MAIN FUNCTIONS_________________________________________________________________PRIMARY
    return {
        /**
         * Get projects and display them in table
         * @param {number} userId
         */
        getProjects: function  (userId) {
            // klic na API
            ajaxCall(
                '/ProjektPraksa/Projects', //'ad_adder_api.php',
                'post', //'post',
                {userId: userId},
                function (data) {
                    addProjectRows(data);
                    /*for (var id in data) if (data.hasOwnProperty(id)) {
                        addProjectRow(data[id]);  }*/
                }
            );
        },

        /**
         * Create new project with new name
         * @param {number} userId
         * @param {string} name
         */
        newProject: function  (userId, name) {
            // preveri pravilnost podatkov
            // naredi AJAX klic za insert
            // feedback uporabniku
            //Regex objem znakov za validacijo stringa
            var regex = /^[A-Za-z0-9]+$/;
            var valid = regex.test(name);

            if (valid) 
            {
                ajaxCall(
                    '/ProjektPraksa/NewProject',
                    'post',
                    {userId: userId, name: name},
                    function (response) { 
                        //FEEDBACK???
                        console.log(response); 
                        alert(response); 

                        //Stran je potrebno osvežiti, lahko pa ponovno kličemo getProjects
                        window.location.reload(1);
                    }
                );
            } else {
                alert("Za ime projekta so dovoljene le črke in števke!");
            }
        },

        /**
         * Open project
         * @param {number} projectId 
         */
        openProject: function (projectId) {
            // Kliči ob kliku na izbran projekt
            // ustvari ustrezen url za pravi projekt
            // preusmeri na ta projekt

            //DEMO SAMO SESSIONSTORAGE ZA PROJECTID
            sessionStorage.setItem("projectId", projectId);
            window.location.href = "materiali.html";

            /*             
            const url = "https://www.encodedna.com/javascript/demo/" +
                "check-if-url-contains-a-given-string-using-javascript.htm?book=" + v1.value  + 
                "&name=" + v2.value;
            window.location.href = url;
            */
        },

   }
})();

//document.getElementById('projekt-ime').removeAttribute('value');

//Nastavimo za DEMO ID uporabnika na 1, in to vrednost dodelimo
const USERID=1; //Ob dodelitvi modula prijave SPREMENITI!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//document.getElementById("userid").setAttribute('value', userid);

//Ob naložitvi strani se kliče funkcija getProjects 
//Ta prikaže vse uporabnikove projekte


window.addEventListener('load', function () { 
    Project.getProjects(USERID);
});

/*
document.getElementById('iframe-projects-table').onload = function() {
    Project.getProjects(USERID);
}
*/

//GUMB - klik gumba v formi new-project naredi API klic za insert novega
document.querySelector('#ustvari').addEventListener('click', function(e) {
    var projectName = document.getElementById('projekt-ime').value;
    //console.log("klik dela, ime: " + projectName); --DELUJE
    //var forma = document.querySelector('#new-project');
    e.preventDefault();
    Project.newProject(USERID, projectName);
});

//GUMB - UstvariNovProjekt se ob kliku skrije in prikaže formo
document.querySelector('#show-form').addEventListener('click', function() {
    document.querySelector('#new-project').removeAttribute('hidden');
    document.querySelector('#show-form').setAttribute('hidden', false);
});
