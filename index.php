<html>
    <head>
        <title>TD Interop</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
            integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
            crossorigin=""/>
        <link rel="stylesheet" href="css/semantic.css">
        <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
            integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
            crossorigin=""></script>
    </head>


    <body>
        <div class="ui top fixed menu" style="z-index: 1">
            <div class="item">
                <h1>Interopérabilité</h1>
            </div>        
        </div>

        <div class="ui grid" style="margin-top: 20px;">
            <div class="eight wide centered row">
                <?php
                    /*
                    Pour le proxy de l'IUT, au besoin
                    $opts = array('http' => array('proxy'=> 'tcp://127.0.0.1:8080', 'request_fulluri'=> true));

                    $context = stream_context_create($opts);
                    */

                    // Par défaut, l'IP utilisé est l'IP courante
                    $ip_link = "http://ip-api.com/xml";
                    $ip_data = file_get_contents($ip_link);
                    $ip_xml = simplexml_load_string($ip_data);

                    $lat_client = $ip_xml->xpath("lat")[0]->__toString();
                    $lng_client = $ip_xml->xpath("lon")[0]->__toString();

                    // On récupère les données météos
                    $meteo_link = "http://www.infoclimat.fr/public-api/gfs/xml?_ll=$lat_client,$lng_client&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2";
                    echo $ip_data;
                    $meteo_data = file_get_contents($meteo_link);
                    $meteo_xml = simplexml_load_string($meteo_data);

                    // On récupère notre feuille XSL
                    $xsl_link = "meteo.xsl";
                    $xsl_data = file_get_contents($xsl_link);
                    $xsl = simplexml_load_string($xsl_data);

                    // On instantie notre processeur XSLT
                    $proc = new XSLTProcessor();
                    // On donne la feuille XSL au processeur
                    $proc->importStyleSheet($xsl);
                    // On transforme notre XML, puis on l'affiche
                    echo $proc->transformToXML($meteo_xml);
                ?>
            </div>
            
            <div id="mapid" style="height: 600px; width: 60%; margin: auto; z-index: 0"></div>            
        </div>

        <script>
            var redIcon = new L.Icon({
                iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            <?php
                echo "var mymap = L.map('mapid').setView([$lat_client, $lng_client], 14);";  
                echo "var marker = L.marker([$lat_client, $lng_client], {icon: redIcon}).addTo(mymap).bindPopup(\"<b>Vous êtes ici !</b>\").openPopup();";          
            
                // On récupère les infos sur les parkings
                $parking_link = "http://www.velostanlib.fr/service/carto";
                $parking_data = file_get_contents($parking_link);
                $parking_xml = simplexml_load_string($parking_data);
                $marker_list = $parking_xml->xpath("markers/marker");

                foreach($marker_list as $marker)
                {
                    $marker_nb = $marker->xpath("@number")[0];
                    $marker_lat = $marker->xpath("@lat")[0];
                    $marker_lng = $marker->xpath("@lng")[0];

                    $parking_info_url = "http://www.velostanlib.fr/service/stationdetails/nancy/$marker_nb";
                    $parking_info_data = file_get_contents($parking_info_url);
                    $parking_info_xml = simplexml_load_string($parking_info_data);

                    $marker_available = $parking_info_xml->xpath("available")[0];
                    $marker_total = $parking_info_xml->xpath("total")[0];
                    $marker_label = "$marker_available / $marker_total";

                    echo "L.marker([$marker_lat, $marker_lng]).addTo(mymap).bindPopup(\"$marker_label\");";
                    
                }
            ?>        

            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
                maxZoom: 18,
                id: 'mapbox.streets',
                accessToken: 'pk.eyJ1IjoiZGVtYXJicmUxdSIsImEiOiJjamN0aDQ4ejMwZmM0MnFudXNmODc1am52In0.F7HMcNCiKY5gygrzGx1obg'
            }).addTo(mymap);    
        </script>
    </body>
</html>