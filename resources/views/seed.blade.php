@extends('layouts.app')
@section('content')
    <div id="element" class="mt-5">
        <div class="loader"></div>
    </div>

    <script type="application/javascript">
        var sites = ['antena3','adevarul','aktual','economica','hotnews','news','agerpress']

        for (let i = 1; i < 30; i++) {
            $.ajax({
                url: '/api/more_info',
                type: "post",
                async: true,
                data: { page: i } ,
                success: function (response) {
                    console.log(i);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }

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
            document.getElementById('element').innerHTML = '<p>The articles were inserted</p>';
            alert('The articles were inserted');
            window.location.replace('/');
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
