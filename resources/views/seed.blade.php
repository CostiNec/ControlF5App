@extends('layouts.app')
@section('content')
    <div id="element" class="mt-5">
        <div class="loader"></div>
    </div>

    <script type="application/javascript">
        var sites = ['antena3','adevarul','aktual','economica','hotnews','news']

        sites.forEach(site => {
            $.ajax({
                url: '/api/get_links',
                type: "post",
                async: true,
                data: { site: site } ,
                success: function (response) {
                    response = JSON.parse(response);
                    response.forEach(url => {
                        parseSiteUrls(site,url);
                    })
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        })

        $( document ).ajaxStop(function(){
            document.getElementById('element').innerHTML = '<p>The articles was inserted</p>';
            alert('The articles was inserted');
        });

        function parseSiteUrls(site, url) {
            $.ajax({
                url: '/api/insert_results',
                type: "post",
                async: true,
                data: { url: url, site: site } ,
                success: function (response) {
                    console.log(site);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    </script>
@endsection
