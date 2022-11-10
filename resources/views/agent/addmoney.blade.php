@extends('layouts.app')

@section('content')
<style>
.form-control {
height:34px !important;
}

</style>
<script type="text/javascript">
   function Callurl()
   {    
            var objSelectedDdl = document.getElementById("circle_id");
            window.open(objSelectedDdl.options[objSelectedDdl.selectedIndex].getAttribute('redirectUrl'),'_blank');
           //open in same tab   //window.location.href=objSelectedDdl.options[objSelectedDdl.selectedIndex].getAttribute('redirectUrl');
    }
</script>

<div class="super_container">

	
	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="">
			<div class="">
				<div class="">

					<!-- Search Tabs -->

					@include('partials.tab')
					
					<br>
	                  <div class="col-md-3" style="margin-left:80px;">
                         <div class="form-group ">
                             <label class="label" style="color:black"> Payment Modes</label>
                                 <input type="checkbox" checked>
                         </div>
                        <div class="form-group ">
                            <label class="label" style="color:black">Gateway Bank</label>
                                
                            <select class="form-control" id="circle_id"  name="circle_id">
                                <option >----Select----</option>
                               <option value="ALLAHABAD BANK" redirectUrl="https://www.allbankonline.in/jsp/startnew.jsp" id="ald">ALLAHABAD BANK</option>
                                <option value="ANDHRA BANK" redirectUrl="https://www.onlineandhrabank.net.in/BankAwayRetail/AuthenticationController?__START_TRAN_FLAG__=Y&FORMSGROUP_ID__=AuthenticationFG&__EVENT_ID__=LOAD&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=011" id="andhraBank">ANDHRA BANK</option>
                                <option value="AXIS BANK" redirectUrl="https://retail.axisbank.co.in/wps/portal/rBanking/AxisSMRetailLogin/axissmretailpage?AuthenticationFG.MENU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&CATEGORY_ID=IR" id="axisBank"> AXIS BANK</option>
                               <option value="BANK OF BAHARAIN AND KUWAIT BSC" redirectUrl="https://ebanking.bbkindia.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=150&LANG_ID=001" id="bobakb">BANK OF BAHARAIN AND KUWAIT BSC</option>
                                <option value="BANK OF BARODA"  redirectUrl="https://www.bobibanking.com/BankAwayRetail/(S(prsqur45ufdxqlby2xlbgx45))/web/L001/retail/jsp/arcot/pages/RetailLogin.aspx?RequestId=186324" id="bob">BANK OF BARODA</option>
                                <option value=" BANK OF INDIA" redirectUrl="https://www.bankofindia.co.in/" id="boi"> BANK OF INDIA</option>
                                <option value="BANK OF MAHARASHTRA" redirectUrl="https://www.mahaconnect.in/jsp/index.html" id="bom">BANK OF MAHARASHTRA</option>
                                <option value="BHARAT COOPERATIVE BANK MUMBAI LIMITED" redirectUrl="https://connect.bharatbank.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=112&AuthenticationFG.MENU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&CATEGORY_ID=PAYU&RU=https%3A%2F%2Fsecure.payu.in%2F348add8278dba6ed6dda4c6139908e89%2FBharatBankNb_response.php&QS=y%2BDgypcGonlllV%2BuUBGaZZn9lD78l6WSPpxey7IMhcXL4ODKlwaieWWVX65QEZplR6p4vzHf7GdrIEqlQt6Vvf5tEqo9xJ5OQeZXpJr9cZY4V9uRRxvBF1rbyTIoCKRvj2S2M0v3rzDPmSjsw832QJDlYrxnAG42m2KwtVAs1SVzFEojercK0HhdBPsHHyOLS6PPJl4NKBUDi6ZG0MAiaqzQsNx%2Bql%2B%2F" id="baharatCooperative">BHARAT COOPERATIVE BANK MUMBAI LIMITED</option>

                                <option value="CANARA BANK" redirectUrl="https://netbanking.canarabank.in/entry/ENULogin.jsp" id="canaraBank">CANARA BANK</option>
                                <option value="CATHOLIC SYRIAN BANK LIMITED" redirectUrl="https://www.csbnet.co.in/Home.aspx#b" id="catholicSyrian">CATHOLIC SYRIAN BANK LIMITED</option>
                                <option value="CENTRAL BANK OF INDIA" redirectUrl="https://www.centralbank.net.in/jsp/startMain.jsp" id="cboi">CENTRAL BANK OF INDIA</option>
                                <option value="CITI BANK" redirectUrl="https://www.onlinecub.net/" id="citiBank">CITI BANK</option>
                               
                                <option value="CORPORATION BANK" redirectUrl="https://corpnetbanking.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=CB&CATEGORY_ID=SHP&AuthenticationFG.TAX_MNU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&RU=https://www.tpsl-india.in/PaymentGateway/PaymentGatewayReturnCorpbank.jsp?MerCD~10027|data~EUgG2q0G/Z15ZHzTH2yjXU6bKVtaYPxkKq+SbB4KhXsLXYKbyhuamu2OoY4CERYeAHq1llMPi01OJE5nYVs0GCZcZlODGrwDbTak+39Xuqc=&QS=XXJt1sfFgH42aDbauaUS8BXcjLS%2FCVixrKVvJORhuLldcm3Wx8WAfjZoNtq5pRLwFWAw3KngDoUitZ8XslX325Mn9nY6NYPcQCpu7v6ZU8pv2UvGJpJezirzim4Hmm7S963rFDS149fAqmxKD3Flsn75bKyTKXIX6P%2F9Yjb6BVoXSl%2FjFVvsEMK5LZE%2F3nJ9lUwBM%2FvR6IVwkpevlr0TD4hXfCkeV4IorbK340kfAPG4Qv2WRpzwHQ4LauoPFLpGtOKzwYVIZwwkuBEjAUbdMQ%3D%3D" id="corporationBank">CORPORATION BANK</option>
                                <option value="COSMOS BANK" redirectUrl="https://online.cosmosbank.in/">COSMOS BANK</option>

                                <option value="DCB BANK - CORPORATE NETBANKING" redirectUrl="https://pib.dcbbank.com/corp/AuthenticationController?__START_TRAN_FLAG__=Y&FORMSGROUP_ID__=AuthenticationFG&__EVENT_ID__=LOAD&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=072&LANGUAGE_ID=001" id="dbbNetbanking">DCB BANK - CORPORATE NETBANKING</option>
                                <option value="DCB BANK LIMITED" redirectUrl="https://pib.dcbbank.com/corp/AuthenticationController?__START_TRAN_FLAG__=Y&FORMSGROUP_ID__=AuthenticationFG&__EVENT_ID__=LOAD&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=072&LANGUAGE_ID=001" id="dcbLimited">DCB BANK LIMITED</option>
                                <option value="DENA BANK" redirectUrl="https://denaiconnect.denabank.co.in/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&REDIR=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=018&AuthenticationFG.USER_TYPE=1&AuthenticationFG.MENU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&RU=https://secure.payu.in/08d17a24e8c8008d5aed2f687b951475/DenaNb_response.php&QS=ShoppingMallTranFG.TRAN_CRN~INR|ShoppingMallTranFG.TXN_AMT~100|ShoppingMallTranFG.PID~000000000073|ShoppingMallTranFG.PRN~7732083445|ShoppingMallTranFG.ITC~2446248|ShoppingMallTranFG.NAR~wwwjaldicashcom" id="denab">DENA BANK</option>
                                <option value="DEUSTCHE BANK" redirectUrl="https://login.deutschebank.co.in/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&__FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=200&JS_ENABLED_FLAG=Y&AuthenticationFG.CALL_MODE=2&LANGUAGE_ID=001" id="deustche">DEUSTCHE BANK</option>
                                <option value="DHANALAKSHMI BANK" redirectUrl="https://netbank.dhanbank.in/DBRetailBank/?encDhanBankTPSLData=32D04BB9CBABCBF4C26863B97607BE6CC889A512E1159E1A58433AA25D3ED091C059A0537DF4B8C406265D0D9F08526446A09AB02C3135237986DA3345631097EA8E5800DBF6EF4E0E5255C037C7E0600FB4D06D9C213BD2B707D33B292021D2570EA211458A0F2AB63593593577E15A568E5E2F543F2B3AB805D98740AE28B3782C1BDA729D40183BC4890C0F57FE307608B2AC1519C81025091F86F925233EF0A8DF12D4AD23D013C396E54B0F238B&cursec=0" id="dhanlakshmi">DHANALAKSHMI BANK</option>

                                <option value="FEDERAL BANK" redirectUrl="https://www.fednetbank.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=049&LANGUAGE_ID=001&JS_ENABLED_FLAG=Y&AuthenticationFG.CALL_MODE=2&" id="federal">FEDERAL BANK</option>
                                <option value="HDFC BANK" redirectUrl="https://netbanking.hdfcbank.com/netbanking/" id="hdfc">HDFC BANK</option>

                                <option value="ICICI Bank - CORPORATE NETBANKING" redirectUrl="https://infinity.icicibank.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=ICI&_ga=2.68391321.949700012.1544435820-364655473.1544435820" id="icicibank"> ICICI Bank - CORPORATE NETBANKING</option>
                                <option value="IDBI BANK" redirectUrl="https://www.idbi.com/idbi-bank-internet-banking.asp" id="idbi"> IDBI BANK</option>
                                <option value="INDIAN BANK" redirectUrl="https://www.indianbank.net.in/jsp/startIB.jsp" id="indianBank"> INDIAN BANK</option>
                                <option value="INDIAN OVERSEAS BANK" redirectUrl="https://www.iobnet.co.in/ibanking/login.do" id="indianOverseas"> INDIAN OVERSEAS BANK</option>
                                <option value="INDUSIND BANK" redirectUrl="https://indusnet.indusind.com/corp/BANKAWAY?Action.RetUser.Init.001=Y&AppSignonBankId=234&AppType=corporate&CorporateSignonLangId=001" id="indusindbank"> INDUSIND BANK</option>

                                <option value="JAMMU AND KASHMIR BANK LIMITED" redirectUrl="https://www.jkbankonline.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&__FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=JKB" id="janataSahakari"> JAMMU AND KASHMIR BANK LIMITED</option>
                                <option value="JANATA SAHAKARI BANK LIMITED" redirectUrl="https://jsbeasynet.jsbnet.in/loginretailmerchant?BankId=JSB&MD=P&PID=00000000026&BRN=7732151593&AMT=100&CRN=INR&RU=https%3A%2F%2Fsecure.payu.in%2F8f596687c88780adf8d7e55ba650f6d9%2FJantaSahakariNB_response.php&ITC=2446364" id="janataSahakari"> JANATA SAHAKARI BANK LIMITED</option>
                                <option value="KARNATAKA BANK LIMITED" redirectUrl="https://moneyclick.karnatakabank.co.in/BankAwayRetail/AuthenticationController?__START_TRAN_FLAG__=Y&FORMSGROUP_ID__=AuthenticationFG&__EVENT_ID__=LOAD&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=KBL&LANGUAGE_ID=001" id="karnatakaBank"> KARNATAKA BANK LIMITED</option>
                                <option value="KARUR VYSYA - CORPORATE NETBANKING" redirectUrl="https://www.kvbin.com/B001/ENULogin.jsp" id="karurVysya"> KARUR VYSYA - CORPORATE NETBANKING</option>
                                <option value="KOTAK MAHINDRA BANK LIMITED" redirectUrl="https://www.kotak.com/j1001mp/netapp/MainPage.jsp" id="kotakMahimdra">KOTAK MAHINDRA BANK LIMITED</option>

                                <option value="LAKSHMI VILAS BANK - CORPORATE NETBANKING" redirectUrl="https://www.lvbank.com" id="lakshmiVilasCorporate">LAKSHMI VILAS BANK - CORPORATE NETBANKING</option>
                               
                                <option value="NAINITAL BANK" redirectUrl="https://netbanking.nainitalbank.co.in:9998/IBankLoginExtends.aspx" id="nanitalBank">NAINITAL BANK</option>
                                <option value="ORIENTAL BANK OF COMMERCE" redirectUrl="https://www.obconline.co.in/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=022&AuthenticationFG.USER_TYPE=1&AuthenticationFG.MENU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&RU=mzEccbwg2KZ3xp1RBvCh6%2Fgwzgtqjmzn0%2Fi8%2B13V1fpOyGriRu9UeFzx7%2FTW2%2BJk3pPMVY2XVzEJ%0D%0Ac5sKrgIO3DM%2BM5Qo9Hjhaf%2BRYeyENbrdQcxTdBrWtPQ%2B2KUtsbIc&CATEGORY_ID=400&QS=%2F5B7Y7PPjoOYiT55Eir1ZFRdDMnTSVq8m4XmlZ6SV2P%2FkHtjs8%2BOg5iJPnkSKvVkQ5vRU5OM5DNr%0D%0Am4L8v%2FrLuu0lzcfEZy2S4Xsq%2BZK4PdvMqF4J5ZyRiufXBpCuILJA14vsgpDBiXURvwZ9Z1p%2BaTYV%2Bjog%0D%0A9nWqS9naTXxICS%2FLuAzm5HxVRHbxxQL7K4YE8puY%2FmvmefgXuNb1A6AdskCmNN8FHCuxXKtCgTxCPQw%3D" id="orientalBank">ORIENTAL BANK OF COMMERCE</option>

                                <option value="PUNJAB AND MAHARSHTRA COOPERATIVE BANK" redirectUrl="https://www.pmcbank.com/english/home.aspx#" id="punjabAndMaharastra">PUNJAB AND MAHARSHTRA COOPERATIVE BANK</option>
                                <option value="PUNJAB AND SIND BANK" redirectUrl="https://www.psbindia.com/" id="punjabAndSind">PUNJAB AND SIND BANK</option>
                                <option value="PUNJAB NATIONAL BANK" redirectUrl="https://netbanking.netpnb.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&__FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=024" id="punjabNational">PUNJAB NATIONAL BANK</option>
                                <option value="RATNAKAR BANK LIMITED" redirectUrl="https://online.rblbank.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=176&AuthenticationFG.MENU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&CATEGORY_ID=AAA&RU=https%3A%2F%2Fwww.tpsl-india.in%2FPaymentGateway%2FPaymentGatewayReturnRBLBankNewVer2.jsp&QS=AZjAAEqTKfmJVz9fJLkoQ%2BMKilHQ4dKBOKCMbXeACJMBmMAASpMp%2BYlXP18kuShDldkHutsNmSmPq42pA3ybinq7lNYQ9ZQLQbyN1Hkh1LGAVRm3aLYP6s5456vrdFLeAZjAAEqTKfmJVz9fJLkoQysot%2BaqwkOC%2FHB3y%2B%2FW9XyH43G5V0eMC%2Bnu2QAOCNr7HPezW%2FhM8c1712o5MPPgg35n11PkusEtjmlMlQp4t98%3D" id="ratanakerBank">RATNAKAR BANK LIMITED</option>

                                <option value="SARASWAT COOPERATIVE BANK LIMITED" redirectUrl="https://onepage.saraswatbank.co.in/netbanking/login" id="saraswatBank">SARASWAT COOPERATIVE BANK LIMITED</option>
                                <option value="SOUTH INDIAN BANK" redirectUrl="https://sibernet.southindianbank.com/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&__FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=059&JS_ENABLED_FLAG=Y&AuthenticationFG.CALL_MODE=0&REDIR=Y&" id="southIndian">SOUTH INDIAN BANK</option>
                                <option value="STANDARD CHARTERED BANK" redirectUrl="https://ibank.standardchartered.co.in/nfs/ddpayments_redirection_login.htm" id="standardCharted">STANDARD CHARTERED BANK</option>
                                <option value="STATE BANK OF BIKANER AND JAIPUR" redirectUrl="https://merchant.onlinesbi.com/">STATE BANK OF BIKANER AND JAIPUR</option>
                                <option value="STATE BANK OF INDIA" redirectUrl="https://merchant.onlinesbi.com/" id="SBI">STATE BANK OF INDIA</option>
                                <option value="SYNDICATE BANK" redirectUrl="https://www.syndonline.in/B001/ENULogin.jsp" id="syndicateBank">SYNDICATE BANK</option>

                                <option value="TAMILNAD MERCANTILE BANK LIMITED" redirectUrl="https://www.tmbnet.in/" id="tamilnad">TAMILNAD MERCANTILE BANK LIMITED</option>
                                <option value="THE SHAMRAO VITHAL COOPERATIVE BANK" redirectUrl="https://www.svcbank.com/" id="shamraoVithal">THE SHAMRAO VITHAL COOPERATIVE BANK</option>

                                <option value="UCO BANK" redirectUrl="https://www.ucoebanking.com/" id="UcoBank">UCO BANK</option>
                                <option value="UNION BANK - CORPORATE NETBANKING" redirectUrl="https://www.unionbankonline.co.in/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=026&AuthenticationFG.USER_TYPE=1&AuthenticationFG.TAX_MNU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&CATEGORY_ID=SHP&RU=https://secure.payu.in/2f5c5d4921a66a30cee4e6c2934709f0/ubi_response.php&QS=XXJt1sfFgH42aDbauaUS8BXcjLS%2FCVixrKVvJORhuLldcm3Wx8WAfjZoNtq5pRLwVh%2BJDtHHTDcOuyBiKfwnB%2Fet6xQ0tePXwKpsSg9xZbLykEGTqJ6kiFhU%2BdOtHMF0%0D%0A3j4Rrre%2FDBbqaX0ML70Engzlin4bKbRviptLMKwc81pIAeC%2BB%2FdXahHS6kw3Wu3DmfVaNQx2XoRraXRMfaRwYnFpA4NIKZ9rqrFiKxwO%2Bglck%2BmJ4Jn3tEkSqTF8Hsf97IxT%0D%0A8D6O%2Bh%2FZSHxgxMeong%3D%3D" id="UnionBank">UNION BANK - CORPORATE NETBANKING</option>
                                <option value="UNION BANK OF INDIA" redirectUrl="https://www.unionbankonline.co.in/corp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=026&AuthenticationFG.USER_TYPE=1&AuthenticationFG.TAX_MNU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&CATEGORY_ID=SHP&RU=https://secure.payu.in/6fe43512af099ca2108c4005402b1105/ubi_response.php&QS=XXJt1sfFgH42aDbauaUS8BXcjLS%2FCVixrKVvJORhuLldcm3Wx8WAfjZoNtq5pRLwVh%2BJDtHHTDcOuyBiKfwnB%2Fet6xQ0tePXwKpsSg9xZbLykEGTqJ6kiFhU%2BdOtHMF0%0D%0A3j4Rrre%2FDBbqaX0ML70Engzlin4bKbRviptLMKwc81pIAeC%2BB%2FdXahHS6kw3Wu3DmfVaNQx2XoRraXRMfaRwYn3M4TsBI5RdgRbvBYXWFvRck%2BmJ4Jn3tEkSqTF8Hsf9L%2Bx3%0D%0A15PgSNZXJfIx59XEAw%3D%3D" id="unionBankOfIndia">UNION BANK OF INDIA</option>
                                <option value="UNITED BANK OF INDIA" redirectUrl="https://www.unitedbankofindia.com/english/Homepage.aspx" id="unitedBankOfIndia">UNITED BANK OF INDIA</option>

                                <option value="VIJAYA BANK" redirectUrl="https://www.vijayabankonline.in/NASApp/AuthenticationController?FORMSGROUP_ID__=AuthenticationFG&__START_TRAN_FLAG__=Y&FG_BUTTONS__=LOAD&ACTION.LOAD=Y&AuthenticationFG.LOGIN_FLAG=1&BANK_ID=029&AuthenticationFG.USER_TYPE=1&AuthenticationFG.TAX_MNU_ID=CIMSHP&AuthenticationFG.CALL_MODE=2&LANGUAGE_ID=001&AuthenticationFG.USER_TYPE=1" id="VijayaBank">VIJAYA BANK</option>
                                <option value="YES BANK" redirectUrl="https://www.yesbank.in/" id="YesBank">YES BANK</option>
                            
                            </select>
                        </div>
                       
                           {{-- <div class="form-group">
                              <label class="label" style="color:black">Amount</label>
                               
                                        <input class="form-control" type="text" value="" name="number" placeholder="Enter Valid Amount" maxlength="10">
                                    
                            </div>--}}
                       
                            <div class="form-group col-md-12" >
                                 <button  type="submit" class="btn btn-success" onclick="Callurl()">Proceed To Pay</button>
                                 <button type="button"  class="btn btn-success">Cancel</button>
                                  
                            </div>
                              
                         
                    </div>
                        		
					
    					
				</div>
			</div>
		</div>		
	</div>
	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection