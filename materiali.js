
var Material = (function () {

    /**
     *  -cards so prikazni prostor za materiale 
     */
    var htmlCards = document.querySelector('#cards'),
        //materiali = document.querySelector('#materiali'),
        projName  = document.querySelector('#project-name-title'),
        formName  = document.querySelector('#project-name-form'),
        modalPop  = document.querySelector('#uploadmodal');
        

    /**
     * Make APi calls to backend
     * @param {string} url 
     * @param {string} method  "POST|GET"
     * @param {object} data 
     * @param {function} callback 
     */
    function ajaxCall (url, method, data, callback) {

        if (!url) { url = 'index.php'; }

        if (!method) { method = 'GET'; }

        if (!data) { data = ''; }

        if (!callback) { callback = function (data) {
                console.log("Manjkajoč callback!");
                console.log(data);
            }
        }

        var xhttp = new XMLHttpRequest(),
            post  = JSON.stringify(data);
            //post  = "post=" + JSON.stringify(data); //JSON.stringify(data);

        if (typeof callback == 'function') {
            xhttp.onreadystatechange = function () {
                if(this.readyState ==4 && this.status == 200)
                {
                    try{
                        //ZA TEST PHP RESPONSE____________
                        //console.log(xhttp.responseText);
                        var response = JSON.parse(xhttp.responseText);
                        //console.log(response);
                    } catch(error){
                        console.log(error.message + " in " + xhttp.responseText);
                        return;
                    }
                    
                    if (response['success'] == true){
                        callback(response['data']);
                        //console.log("succesfull response");
                    } else {
                        alert("Ajax response not successfull")
                    }
                }
            }
        };
        console.log(post);

        xhttp.open(method.toUpperCase(), url, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(post);
    }

    /**
     * Create material display card
     * @param {number} id
     * @param {string} ime
     * @param {string} tip
     * @param {string} dimen
     * @param {number} velik
     * @param {string} refer
     */
    function createCard(id, ime, tip, dimen, velik, refer) {

        var bend = "KB",
            size = velik / 1024;

        if (size > 1000) { 
            size  = size / 1024; 
            bend = "MB";
        }
        size = size.toFixed(2);

        //pridobim celotno ime materiala, za prikaz na strani
        var name = refer.split('\\').pop().split('/').pop();
        //console.log(name);

        var cardShel = document.createElement('div'),
            cardBody = document.createElement('div'),
            cardData = document.createElement('div'),
            cardBttn = document.createElement('div'),
            cardText = document.createElement('p'),
            btnDelet = document.createElement('button'),
            btnUpdat = document.createElement('button');

    
        var trashIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">';
        trashIcon += '<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3';
        trashIcon += ' .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1';
        trashIcon += ' 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882';
        trashIcon += ' 4H4.118zM2.5 3V2h11v1h-11z"/></svg>'

        var pencilIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">';
        pencilIcon += '<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 ';
        pencilIcon += '.11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 ';
        pencilIcon += '0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 ';
        pencilIcon += '1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>';

        cardShel.setAttribute('class', 'col-md-4 p-1');
        cardBody.setAttribute('class', 'card md-4 box-shadow border border-secondary');
        cardData.setAttribute('class', 'card-body d-flex justify-content-between');
        cardData.setAttribute('style', 'background: aliceblue');
        cardBttn.setAttribute('class', 'btn-group');
        cardText.setAttribute('class', 'card-text text-secondary');
        btnDelet.setAttribute('class', 'btn btn-sm btn-outline-secondary');
        btnDelet.setAttribute('title', 'Izbriši')
        btnUpdat.setAttribute('class', 'btn btn-sm btn-outline-secondary');
        btnUpdat.setAttribute('title', 'Spremeni ime');

        //cardMBox.setAttribute('class', 'box');
        //cardMBox.setAttribute('style', 'height: 100%; width: 100%; display: block;');

        //USTVARIM HTML element za prikaz materiala
        if (tip == 'mp4') {
            var content = document.createElement('video');
            content.setAttribute('controls', 'controls');
        } else {
            var content = document.createElement('img');
        }
        content.setAttribute('class', 'card-img-top');
        content.setAttribute('src', 'materiali/'+name); //LOKACIJA MATERIALA ZA PRIKAZ
        content.setAttribute('style', 'height: 100%; width: 100%; display: block;');
        cardText.innerHTML = "<strong>" + ime + "</strong><br>" + tip + " | " + dimen + "px | " + size + bend;
        btnDelet.innerHTML = trashIcon;
        btnUpdat.innerHTML = pencilIcon;

        btnUpdat.addEventListener('click', function() {
            Material.updateMaterial(id);
            console.log("PENCIL");
         });

        btnDelet.addEventListener('click', function() {
           // Material.deleteMaterial(id); NONEXISTENT
            console.log("TRASH");
        });
        cardBttn.appendChild(btnUpdat);
        cardBttn.appendChild(btnDelet);

        cardData.appendChild(cardText);
        cardData.appendChild(cardBttn);

        cardBody.appendChild(content);//cardMBox);
        cardBody.appendChild(cardData);
        cardShel.appendChild(cardBody);

        return cardShel;
    }

    /**
     * Create and display material cards
     * @param {object} data 
     */
    function addMaterials(data) {

        let name = data['name'];
        let node = document.createTextNode(name);

        //Append name to update project name form
        formName.setAttribute('placeholder', name);

        //Append project name to header
        projName.setAttribute('value', name);
        projName.appendChild(node);

        //var html1 = "";

        data['files'].forEach( function(file) 
        {
            var id    = file['id'],
                ime   = file['ime'],
                tip   = file['tip'],
                dimen = file['dimenzija'],
                velik = file['velikost'],
                refer = file['referenca'];
            
            var card  = createCard(id, ime, tip, dimen, velik, refer);

            htmlCards.appendChild(card);

            //html1 += "<tr><td>"+id+"</td><td>"+ime+"</td><td>"+tip+"</td><td>"+dimenzija+"</td>";
            //html1 += "<td>"+velikost+"</td><td>"+referenca+"</td><tr>"
        });
        //materiali.innerHTML = html1;
    }

    //MAIN FUNCTIONS____________________________________________________PRIMARY
    return {
        /**
         * Get project's materials & display
         * @param {number} projectId
         */
        getMaterials: function  (projectId) {
            // klic na API
            ajaxCall(
                '/ProjektPraksa/Materials',
                'post',
                {projectId: projectId},
                function (data) {
                    addMaterials(data);
                    /*
                    for (var id in data) if (data.hasOwnProperty(id)) {
                        addProjectRow(data[id]);
                    }
                    */

                }
            );
        },

        /**
         * Check if new project name is valid
         * @param {number} projectId
         * @param {string} oldName
         * @param {string} newName
         */

        /**
         * Check if new project name is valid
         * @param {number} matId
         * @param {string} newName
         * @param {string} comment
         */
        updateMaterial: function (matId) {
            // pridobi ustrezne spremembe
            // naredi AJAX klic za update
            // feedback uporabniku
            ajaxCall(
                '/ProjektPraksa/UpdateMaterial',
                'post',
                {matId: matId, newName: newName, comment: comment},
                function (data) { 
                    //CALLBACK!!! itak dobi upadated ob refreshu gg
                    console.log("Material updated _?");
                    console.log(data);
                }
            );
        },

        /**
         * Update name of this project
         * @param {number} projectId
         * @param {string} newName
         */
        updateProject: function (projectId, newName) {
            // pridobi ustrezne spremembe
            // naredi AJAX klic za update
            // feedback uporabniku
            ajaxCall(
                '/ProjektPraksa/UpdateProject',
                'post',
                {projectId: projectId, newName: newName},
                function (data) { 
                    //CALLBACK!!! itak dobi novo ime ob refreshu gg
                    console.log(data);

                }
            );
        },

        /**
         * Delete this project
         * @param {number} projectId
         */
         deleteProject: function (projectId) {
            // naredi AJAX klic za izbris
            // feedback
            ajaxCall(
                '/ProjektPraksa/DeleteProject',
                'post',
                {projectId: projectId},
                function (data) { 
                    //CALLBACK!!! itak dobi novo ime ob refreshu gg
                    console.log(data);
                    //ODPREM NAZAJ NA MENI PROJEKTOV KO JE TRENUTNI IZBRISAN
                    window.location.href = "projekti.html";

                }
            );
        },

        /**
         * Initialize modal responsiveness
         * Initialize Dropzone parameters
         * @param {number} projectId
         */
        modalWorks: function  (projectId) { 
            //Vzpostavi delujoče gumbe
            //Vzpostavi dropzone

            //myDropzone.removeAllFiles() -odstrani vse materiale, mogoče gumb?!?

            //myDropzone.disable() -mogoče ko zapremo modal da ne uporabljamo procese clienta
            //myDropzone.enable() -ob odprtju modala

            var myDropzone = new Dropzone('.dropzone', {
                url: 'upload_material.php',

                //types of accepted filetypes
                acceptedFiles: 'image/*, video/*',

                //name of post parameter on server
                paramName: "file",// 'file[]' if uploadMultiple=true

                //set max files in DZ space
                //maxFiles: 5,

                //number of max files on paralell upload
                parallelUploads: 10,

                //add link to remove added file in DZ
                addRemoveLinks: true,

                //sending multiple files in one request
                uploadMultiple: true,

                //prevent files from uploading, call .processQueue() to send
                autoProcessQueue: false,

                //params: {"projectid": projectId},

            //DICT Messages of interactions, translated -Slovenian

                dictDefaultMessage: "Odložite materiale tukaj.",

                //dictFallbackMessage: "Vaš brskalnik ne podpira drag&drop funkcije.",

                //dictFileTooBig: "Datoteka je prevelika. Največja dovoljena velikost je {$$}.",

                dictInvalidFileType: "Ta tip datoteke ne podpiramo.",

                //dictResponseError: "Server responded with {{statusCode}} code.",

                dictCancelUpload: "Prekliči nalaganje",

                dictUploadCanceled: "Nalaganje preklicano.",

                dictCancelUploadConfirmation: "Ste prepričani da želite preklicati nalaganje?",

                //for removing files from DZ
                dictRemoveFile: "Odstrani",

                //if null, no prompt warning before removing file
                dictRemoveFileConfirmation: null, //"Are you sure you want to cancel this upload?"

                dictMaxFilesExceeded: "Hkrati dovoljeno nalaganje le 10 materialov.",//"You can not upload any more files."

                success: function(file, response) 
                { 
                    //On successfull file upload, for user FEEDBACK!!!!
                    console.log(response); //server echoes
                },

                init: function() 
                {
                    // also send pId with files to api
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("projectId", projectId); 
                    });

                    // refresh page on completed uploads
                    this.on("completemultiple", function(progress) {
                        console.log('On complete upload refresh page');

                        setTimeout(function () {
                            window.location.reload(1);
                        }, 2000);
                    });
                    //this.on("addedfile", function(file) { console.log("Added file"); });
                }
                
            });

            //Odpri modal
            document.querySelector('#showmodal').addEventListener('click', function () {
                modalPop.style.display = "block";
                modalPop.className     = "modal fade show";
            });

            //Skrij modal
            document.querySelector('#hidemodal').addEventListener('click', function() {
                modalPop.style.display = "none";
                modalPop.className     = "modal fade";
            });

            //Shrani files gumb
            document.querySelector('#upload-files').addEventListener('click', function() {
                myDropzone.processQueue();
                //modalPop.style.display="none";
                //modalPop.className="modal fade";
                
            });
        },

        /**
         * Check if new project name is valid
         * @param {number} projectId
         * @param {string} oldName
         * @param {string} newName
         */
        validateUpdate: function (projectId, oldName, newName) {

            var regex = /^[A-Za-z0-9]+$/;
            var valid = regex.test(newName);

            //preveri če je regex OK in novo ime različno starega
            if (valid && !(newName.normalize() === oldName.normalize())) 
            {
                Material.updateProject(projectId, newName);
                /*
                ajaxCall(
                    '/ProjektPraksa/UpdateProject',
                    'post',
                    {projectId: projectId, newName: newName},
                    function (response) { 
                        //FEEDBACK???
                        console.log(response); 
                        alert(response); 

                        //Stran je potrebno osvežiti, lahko pa ponovno kličemo getProjects
                        window.location.reload(1);
                    }
                );
                */
            } else if (valid) {
                alert("Za ime projekta so dovoljene le črke(angleške abecede) in števke!");
            } else {
                alert("Vnešeno ime je enako trenutnem");
            }
        },

        /**
         * Initialize project name update form
         * Initialize update button responsiveness
         * @param {number} projectId
         */
        settingWorks: function  (projectId) { 

            var gearsSettings = document.querySelector('#settings-project'),
                arrowLeftSett = document.querySelector('#settings-show'),
                arrowRightSet = document.querySelector('#settings-hide'),
                buttonUpdateN = document.querySelector('#settings-project-name'),
                buttonDeleteP = document.querySelector('#settings-project-delete'),
                alertOnDelete = document.querySelector('#delete-project-alert');

            //GUMB - PUŠČICA toggle prikaz nastavitev
            document.querySelector('#settings-project-toggle').addEventListener('click', function() {
                var status = document.querySelector('#settings-hide').style.display;
                if (status == 'none' ) {
                    buttonUpdateN.style.display="block";
                    buttonDeleteP.style.display="block";         
                    arrowRightSet.style.display="block";     
                    arrowLeftSett.style.display="none";
                } else {
                    buttonUpdateN.style.display="none";
                    buttonDeleteP.style.display="none"; 
                    arrowRightSet.style.display="none";      
                    arrowLeftSett.style.display="block"; 
                }
                     
            });
            
            //GUMB - GEARS prikaži nastavitve
            gearsSettings.addEventListener('click', function() {
                buttonUpdateN.style.display="block";
                buttonDeleteP.style.display="block";         
                arrowRightSet.style.display="block";     
                arrowLeftSett.style.display="none";         
            });

            //GUMB - prikaži alert za izbris
            buttonDeleteP.addEventListener('click', function() {
                alertOnDelete.style.display="block";           
            });

            //GUMB - skrij alert za izbris
            document.querySelector('#delete-project-no').addEventListener('click', function() {
                alertOnDelete.style.display="none";         
                buttonUpdateN.style.display="none";
                buttonDeleteP.style.display="none"; 
                arrowRightSet.style.display="none";  
                arrowLeftSett.style.display="block";
            });


            //GUMB - prikaži formo za spremembo imena in skrij nastavitve
            buttonUpdateN.addEventListener('click', function() {
                document.querySelector('#update-project-name-form').style.display="block";
                document.querySelector('#settings-project-toggle').style.display="none";
                gearsSettings.style.display="none"
                buttonUpdateN.style.display="none";
                buttonDeleteP.style.display="none";            
            });

            //GUMB - Prekliči spremembo imena / skrij formo
            document.querySelector('#hide-update-name-form').addEventListener('click', function(e) {
                document.querySelector('#update-project-name-form').style.display="none";
                document.querySelector('#settings-project-toggle').style.display="block";
                gearsSettings.style.display="block"
                buttonUpdateN.style.display="none";
                buttonDeleteP.style.display="none"; 
                arrowRightSet.style.display="none";      
                arrowLeftSett.style.display="block"; 
                e.preventDefault();
            });

            //GUMB - Sprejmi spremembo imena in pošlji ajax klic
            document.querySelector('#update-name-form-button').addEventListener('click', function(e) {
                var oldName = document.getElementById('project-name-title').getAttribute('value'),
                    newName = document.getElementById('project-name-form').value;
                //Preveri ali je vnešeno drugačno ime & preveri za ustrezne znake
                Material.validateUpdate(projectId, oldName, newName);
                e.preventDefault();
            });
        }

   }
})();

//Z localStorage pridobimo projectId trenutnega projekta, LE ZA DEMO!!!!!!!!!!
//const USERID    = 1;
var   projectId = sessionStorage.getItem("projectId");

//cancells automatic DZ detection, AVOIDS(Uncaught Error: Dropzone already attached.)
Dropzone.autoDiscover = false;

//Ob naložitvi strani se kliče funkcija getProjects 
//Ta prikaže vse uporabnikove projekte
window.addEventListener('load', function () { 
    Material.getMaterials(projectId);
    //Material.getProjectName(projectId);
    Material.modalWorks(projectId);
    Material.settingWorks(projectId);
});
