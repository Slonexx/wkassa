



@extends('widget.widget')

@section('content')

    <script>
        const hostWindow = window.parent;
        let Global_messageId = 0;
        let Global_accountId = "{{$accountId}}";
        let Global_object_Id;
        let entity_type = 'salesreturn';

        window.addEventListener("message", function(event) {
            const receivedMessage = event.data;
            $('#workerAccess_yes').show();
            if (receivedMessage.name === 'Open') {
                Global_object_Id = receivedMessage.objectId;
                let params = {
                    accountId: Global_accountId,
                    entity_type: entity_type,
                    objectId: Global_object_Id,
                };
                let url = "{{Config::get("Global")['url']}}" + 'widget/InfoAttributes/';
                let final = url + formatParams(params);

                const xmlHttpRequest = new XMLHttpRequest();
                xmlHttpRequest.addEventListener("load", function() {
                    var json = JSON.parse(this.responseText);
                    console.log(json.ticket_id);
                    let btnF = window.document.getElementById('btnF')
                    let TIS_search = window.document.getElementById('TIS_search')

                    if (json.ticket_id == null){
                        btnF.innerText = 'Фискализация';
                        window.document.getElementById('messageGoodAlert').style.display = 'none'
                        window.document.getElementById("messageGoodAlert").innerText = ""
                        TIS_search.style.display = 'none'
                    } else {
                        btnF.innerText = 'Действие с чеком';
                        window.document.getElementById('messageGoodAlert').style.display = 'block'
                        window.document.getElementById("messageGoodAlert").innerText = "Чек уже создан. Фискальный номер:  " + json.ticket_id
                        TIS_search.style.display = 'block'
                    }

                    var sendingMessage = {
                        name: "OpenFeedback",
                        correlationId: receivedMessage.messageId
                    };
                    hostWindow.postMessage(sendingMessage, '*');
                });
                xmlHttpRequest.open("GET", final);
                xmlHttpRequest.send();
            }

        });
        function formatParams(params) {
            return "?" + Object
                .keys(params)
                .map(function (key) {
                    return key + "=" + encodeURIComponent(params[key])
                })
                .join("&")
        }
        function fiscalization(){

            Global_messageId++;
            var sendingMessage = {
                name: "ShowPopupRequest",
                messageId: Global_messageId,
                popupName: "salesreturnPopup",
                popupParameters: {
                    object_Id:Global_object_Id,
                    accountId:Global_accountId,
                    entity_type:entity_type,
                },
            };
            logSendingMessage(sendingMessage);
            hostWindow.postMessage(sendingMessage, '*');
        }


        function logSendingMessage(msg) {
            var messageAsString = JSON.stringify(msg);
            console.log("← Sending" + " message: " + messageAsString);
        }

        function getSearchToTIS(){
            window.open('https://test.ukassa.kz/kassa/report/search/')
        }

    </script>


    <div class="row gradient rounded p-2">
        <div class="col-6">
            <div class="mx-2"> <img src="https://ukassa.kz/_nuxt/img/d2b49fb.svg" width="90%"   alt=""> </div>
        </div>
        <div class="col-2 ">

        </div>
    </div>

    <div id="messageGoodAlert" class=" mt-1 mx-3 p-2 alert alert-success text-center " style="display: none; font-size: 12px; margin-bottom: 5px !important;">    </div>

    <div id="workerAccess_yes" class="mt-1 mx-4 text-center" style="display:none;">
        <div class="row">
            <div class="col-6">
                <button id="btnF" onclick="fiscalization()" class="btn p-1 btn-warning text-white rounded-pill" style="font-size: 14px">  </button>
            </div>
            <div class="col-6">
                <button id="TIS_search" onclick="getSearchToTIS()" class="btn p-1 btn-info text-white rounded-pill" style="font-size: 14px"> Посмотреть в кассе </button>
            </div>
        </div>
    </div>



@endsection
