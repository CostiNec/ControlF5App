@extends('layouts.app')

@section('content')
    <div id="out-search">

        <div class="top d-flex">
            <div onclick="openNav()" class="open-sidenav">
                <svg style="fill:#757575;width:24px;height:24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"></path><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
            </div>
            <div class="ml-3 mt-3px">
                <p class="top-selected pb-border px-2">TOATE</p>
            </div>
            <div class="ml-3 mt-3px">
                <p class="top-unselected">IMAG.</p>
            </div>

            <div class="order-2 ml-auto google-color">
                <i class="fas fa-th"></i>
            </div>
            <div class="order-2 ml-3">
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </div>

        <div class="container">
            <div style="text-align:center;margin:36px auto 18px;width:160px;line-height:0" id="hplogoo">  <img style="border:none;margin:8px 0" height="56" src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_160x56dp.png" width="160" id="hplogo" alt="Google" data-atf="1"></div>
            <div class="input-group md-form form-sm form-2 pl-0">
                <input onclick="openSearch()" class="form-control h-unset my-0 py-1 amber-border left-search-border" id="keywords1" type="text" aria-label="Search">
                <div class="input-group-append">
                    <span onclick="redirectTo(document.getElementById('keywords2').value)" class="input-group-text amber lighten-3 right-search-border custom-padding" id="basic-text1">
                        <i class="fas fa-search text-grey" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div id="search" style="display: none">
        <div class="d-flex">
            <div class="ml-4 mt-3" onclick="closeSearch()">
                <p class="google-color-blue"><i class="fas fa-arrow-left fa-lg"></i></p>
            </div>
            <div class="mt-2 ml-4 w-100">
                <input class="w-100 h-90 border-0 remove-default-input" onkeyup="getSuggestions(true)" id="keywords2"  type="text" name="keywords">
            </div>
            <div class="ml-4 mt-3" onclick="removeKeyword()" id="remove-keyword" style="display: none">
                <p class="google-color"><i class="fas fa-times fa-lg"></i></p>
            </div>
            <div class="mx-4 mt-3" onclick="redirectTo(document.getElementById('keywords2').value)">
                <p class="google-color-blue"><i class="fas fa-search fa-lg"></i></p>
            </div>
        </div>
        <hr class="my-0 mr-3">
        <div id="suggestions" class="mx-3" onscroll="newRequest()">

        </div>
    </div>

    <script type="application/javascript">
        var skip = 0;
        function openNav() {
            let content = document.getElementById('put-grey');
            console.log(content)
            content.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            content.style.height = '100%';
            content.style.width = '100%';
            content.style.zIndex = '1';
            document.getElementById("sidenav").style.paddingLeft = "15px";
            document.getElementById("sidenav").style.width = "250px";
        }

        function closeNav() {
            let content = document.getElementById('put-grey');
            content.removeAttribute("style")
            document.getElementById("sidenav").style.width = "0";
            document.getElementById("sidenav").style.paddingLeft = "0";

        }

        function openSearch() {
            document.getElementById('keywords2').click();
            let search = document.getElementById('search');
            let content = document.getElementById('out-search');

            content.style.display = 'none';
            search.removeAttribute("style")

        }

        function closeSearch() {
            let keywords1 = document.getElementById('keywords1');
            let keywords2 = document.getElementById('keywords2');
            keywords1.value = keywords2.value;

            let search = document.getElementById('search');
            let content = document.getElementById('out-search');

            search.style.display = 'none';
            content.removeAttribute("style")
        }

        document.body.addEventListener('click', function (event) {

            let sidenav = document.getElementById('sidenav');

            if (!sidenav.contains(event.target) && sidenav.offsetWidth === 250) {
                closeNav();
            }
        });

        function getSuggestions(erase) {
            let keyword = document.getElementById('keywords2').value;
            let suggestions = document.getElementById('suggestions');

            if (keyword.length > 0) {
                document.getElementById('remove-keyword').removeAttribute('style');
            } else {
                suggestions.innerHTML = '';
                document.getElementById('remove-keyword').style.display = 'none';
            }

            if (keyword.length > 0) {
                $.ajax({

                    url : '/api/get_suggestions_info',
                    type : 'POST',
                    data : {
                        'input' : keyword,
                        'skip' : skip
                    },
                    success : function(data) {
                        if( erase ) {
                            suggestions.innerHTML = '';
                            skip = 0;
                        } else {
                            document.getElementById('scrolled').removeAttribute('id');
                            skip++;
                        }
                        data = JSON.parse(data);

                        for (let index = 0; index < data.length; index++) {
                            console.log(data[index])
                            let dFlex = document.createElement('div');
                            dFlex.setAttribute('class','d-flex mt-2');

                            if(index === data.length - 1) {
                                dFlex.setAttribute('id','scrolled')
                            }

                            let i = document.createElement('i');
                            i.setAttribute('class','fas fa-search fa-lg google-color mt-1');
                            i.setAttribute('onclick','redirectTo("' + data[index].keywords + '")');

                            let a = document.createElement('a');
                            a.setAttribute('class','ml-3 pb-2 font-1rem');
                            a.setAttribute('id','suggestion' + data[index].id);
                            a.setAttribute('data-name',data[index].keywords);
                            a.setAttribute('onclick','redirectTo("' + data[index].keywords + '")');
                            a.innerHTML = boldFind(keyword,data[index].keywords);

                            let span = document.createElement('span');
                            span.setAttribute('class','order-2 ml-auto google-color mt-1');
                            span.setAttribute('onclick', 'putSuggestion(' + data[index].id + ')')
                            span.innerHTML = '&#8598;';

                            dFlex.appendChild(i);
                            dFlex.appendChild(a);
                            dFlex.appendChild(span);

                            suggestions.appendChild(dFlex);
                        }
                    },
                    error : function(request,error)
                    {
                        alert("Request: "+JSON.stringify(request));
                    }
                });

            }
        }

        function RemoveAccents(str) {
            var accents    = 'ÀÁÂÃĂÄÅàáăâãäåßÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñȘŠšșŸÿýŽžŢȚțţ';
            var accentsOut = "AAAAAAAaaaaaaaBOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSSssYyyZzTTtt";
            str = str.split('');
            var strLen = str.length;
            var i, x;
            for (i = 0; i < strLen; i++) {
                if ((x = accents.indexOf(str[i])) != -1) {
                    str[i] = accentsOut[x];
                }
            }
            return str.join('');
        }

        function boldFind(keyword, suggestion) {
            suggestion = RemoveAccents(suggestion)
            keyword = RemoveAccents(keyword)
            let start = suggestion.indexOf(keyword);

            if(start === -1)
                return '<strong>' + suggestion + '</\strong>';

            let end = start + keyword.length;

            let string = '<strong>';

            for (let i = 0; i < start; i++) {
                string += suggestion[i];
            }

            string += '</\strong>';

            for (let i = start; i < end; i++) {
                string += suggestion[i];
            }

            string += '<strong>';

            for (let i = end; i < suggestion.length; i++) {
                string += suggestion[i];
            }

            string += '</\strong>';

            return string;
        }

        function removeKeyword() {
            document.getElementById('remove-keyword').style.display = 'none';
            let suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';
            document.getElementById('keywords2').value = '';
        }

        function isScrolledIntoView(el) {
            var rect = el.getBoundingClientRect();
            var elemTop = rect.top;
            var elemBottom = rect.bottom;

            // Only completely visible elements return true:
            var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
            // Partially visible elements return true:
            //isVisible = elemTop < window.innerHeight && elemBottom >= 0;
            return isVisible;
        }

        function newRequest() {
            if (isScrolledIntoView(document.getElementById('scrolled'))) {
                getSuggestions(false);
            }
        }

        function putSuggestion(id) {
            document.getElementById('keywords2').value = document.getElementById('suggestion'+id).getAttribute('data-name');
            getSuggestions(true);
        }

        function redirectTo(search) {
            // let search = document.getElementById('keywords2').value;
            if (search.length > 0) {
                let url = '/results?q=' + search;
                window.location.replace(url);
            }
        }

        $(document).on('keypress',function(e) {
            if(e.which == 13) {
                redirectTo(document.getElementById('keywords2').value)
            }
        });
    </script>
@endsection
