<style>
.nav-tabs>li>a
{
color:white;
}
</style>
<ul class="nav nav-tabs" style="margin-left:95px">
                        <li class="{{(Request::is('*pancard') ? 'active' : '')}}">
                            <a href="{{route('agent-pancard')}}"><span>&nbsp;Pan Activation </span></a>
                        </li>

                         <li class="{{Request::is('*pancard-cardload') ? 'active' : ''}}">
                            <a href="{{route('pancard-cardload')}}"><span>&nbsp;Card Load </span></a>
                        </li>
                        <li class="{{Request::is('*pancard-psalogin') ? 'active' : ''}}">
                            <a href="https://www.psaonline.utiitsl.com/psaonline/"><span>&nbsp;PSA Login</span></a>
                        </li>
                       
                         
                   </ul
