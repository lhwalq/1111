<?php if (!defined('THINK_PATH')) exit(); $daodao = D("fenlei")->where(array("model" => 1, "parentid" => 0))->order("`order` DESC")->LIMIT("15")->select(); $guanggao = D("guanggao_shuju")->where(array("title" => "首页头", "parentid" => 0))->find(); $lou = '-22'; $user_arr = R("Base/huode_user_arr"); $user_name = R("Base/huode_user_name",array($user_arr,"username")); $pinpin = D("pinpai")->where(array("status" => "Y"))->order("`order` DESC")->select(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head> 
        <meta charset="UTF-8" /> 
       
        <meta name="keywords" content="<?php if((isset($guanjianzi)) ): echo ($guanjianzi); else: echo C('web_key'); endif; ?>" />
        <meta name="description" content="<?php if((isset($miaoshu)) ): echo ($miaoshu); else: echo C('web_des'); endif; ?>" /> 
        <meta name="renderer" content="webkit" /> 
        <meta http-equiv="X-UA-Compatible" content="IE = edge" /> 
        <meta property="qc:admins" content="2663047012117353636" />
        <title><?php if((isset($biaoti)) ): echo ($biaoti); else: echo C("web_name"); endif; ?></title>
        <link rel="stylesheet" type="text/css" href="/Public/piyungou/css/Comm.css"/>
        <link rel="stylesheet" type="text/css" href="/Public/piyungou/css/register.css"/>
    <?php if((C("ssc")) ): ?><link rel="stylesheet" type="text/css" href="/Public/style/css/goodsssc.css" /> 
        <?php else: ?>
        <link rel="stylesheet" type="text/css" href="/Public/style/css/goods.css" /><?php endif; ?>
    <script type="text/javascript" src="/Public/plugin/style/global/js/jquery-1.8.3.min.js"></script>
    <style class="firebugResetStyles" type="text/css" charset="utf-8">
        .firebugResetStyles {
            z-index: 2147483646 !important;
            top: 0 !important;
            left: 0 !important;
            display: block !important;
            border: 0 none !important;
            margin: 0 !important;
            padding: 0 !important;
            outline: 0 !important;
            min-width: 0 !important;
            max-width: none !important;
            min-height: 0 !important;
            max-height: none !important;
            position: fixed !important;
            transform: rotate(0deg) !important;
            transform-origin: 50% 50% !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent none !important;
            pointer-events: none !important;
            white-space: normal !important;
        }
        style.firebugResetStyles {
            display: none !important;
        }

        .firebugBlockBackgroundColor {
            background-color: transparent !important;
        }

        .firebugResetStyles:before, .firebugResetStyles:after {
            content: "" !important;
        }
        /**actual styling to be modified by firebug theme**/
        .firebugCanvas {
            display: none !important;
        }

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        .firebugLayoutBox {
            width: auto !important;
            position: static !important;
        }

        .firebugLayoutBoxOffset {
            opacity: 0.8 !important;
            position: fixed !important;
        }

        .firebugLayoutLine {
            opacity: 0.4 !important;
            background-color: #000000 !important;
        }

        .firebugLayoutLineLeft, .firebugLayoutLineRight {
            width: 1px !important;
            height: 100% !important;
        }

        .firebugLayoutLineTop, .firebugLayoutLineBottom {
            width: 100% !important;
            height: 1px !important;
        }

        .firebugLayoutLineTop {
            margin-top: -1px !important;
            border-top: 1px solid #999999 !important;
        }

        .firebugLayoutLineRight {
            border-right: 1px solid #999999 !important;
        }

        .firebugLayoutLineBottom {
            border-bottom: 1px solid #999999 !important;
        }

        .firebugLayoutLineLeft {
            margin-left: -1px !important;
            border-left: 1px solid #999999 !important;
        }

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        .firebugLayoutBoxParent {
            border-top: 0 none !important;
            border-right: 1px dashed #E00 !important;
            border-bottom: 1px dashed #E00 !important;
            border-left: 0 none !important;
            position: fixed !important;
            width: auto !important;
        }

        .firebugRuler{
            position: absolute !important;
        }

        .firebugRulerH {
            top: -15px !important;
            left: 0 !important;
            width: 100% !important;
            height: 14px !important;
            background: url("data:image/png,%89PNG%0D%0A%1A%0A%00%00%00%0DIHDR%00%00%13%88%00%00%00%0E%08%02%00%00%00L%25a%0A%00%00%00%04gAMA%00%00%D6%D8%D4OX2%00%00%00%19tEXtSoftware%00Adobe%20ImageReadyq%C9e%3C%00%00%04%F8IDATx%DA%EC%DD%D1n%E2%3A%00E%D1%80%F8%FF%EF%E2%AF2%95%D0D4%0E%C1%14%B0%8Fa-%E9%3E%CC%9C%87n%B9%81%A6W0%1C%A6i%9A%E7y%0As8%1CT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AATE9%FE%FCw%3E%9F%AF%2B%2F%BA%97%FDT%1D~K(%5C%9D%D5%EA%1B%5C%86%B5%A9%BDU%B5y%80%ED%AB*%03%FAV9%AB%E1%CEj%E7%82%EF%FB%18%BC%AEJ8%AB%FA'%D2%BEU9%D7U%ECc0%E1%A2r%5DynwVi%CFW%7F%BB%17%7Dy%EACU%CD%0E%F0%FA%3BX%FEbV%FEM%9B%2B%AD%BE%AA%E5%95v%AB%AA%E3E5%DCu%15rV9%07%B5%7F%B5w%FCm%BA%BE%AA%FBY%3D%14%F0%EE%C7%60%0EU%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5JU%88%D3%F5%1F%AE%DF%3B%1B%F2%3E%DAUCNa%F92%D02%AC%7Dm%F9%3A%D4%F2%8B6%AE*%BF%5C%C2Ym~9g5%D0Y%95%17%7C%C8c%B0%7C%18%26%9CU%CD%13i%F7%AA%90%B3Z%7D%95%B4%C7%60%E6E%B5%BC%05%B4%FBY%95U%9E%DB%FD%1C%FC%E0%9F%83%7F%BE%17%7DkjMU%E3%03%AC%7CWj%DF%83%9An%BCG%AE%F1%95%96yQ%0Dq%5Dy%00%3Et%B5'%FC6%5DS%95pV%95%01%81%FF'%07%00%00%00%00%00%00%00%00%00%F8x%C7%F0%BE%9COp%5D%C9%7C%AD%E7%E6%EBV%FB%1E%E0(%07%E5%AC%C6%3A%ABi%9C%8F%C6%0E9%AB%C0'%D2%8E%9F%F99%D0E%B5%99%14%F5%0D%CD%7F%24%C6%DEH%B8%E9rV%DFs%DB%D0%F7%00k%FE%1D%84%84%83J%B8%E3%BA%FB%EF%20%84%1C%D7%AD%B0%8E%D7U%C8Y%05%1E%D4t%EF%AD%95Q%BF8w%BF%E9%0A%BF%EB%03%00%00%00%00%00%00%00%00%00%B8vJ%8E%BB%F5%B1u%8Cx%80%E1o%5E%CA9%AB%CB%CB%8E%03%DF%1D%B7T%25%9C%D5(%EFJM8%AB%CC'%D2%B2*%A4s%E7c6%FB%3E%FA%A2%1E%80~%0E%3E%DA%10x%5D%95Uig%15u%15%ED%7C%14%B6%87%A1%3B%FCo8%A8%D8o%D3%ADO%01%EDx%83%1A~%1B%9FpP%A3%DC%C6'%9C%95gK%00%00%00%00%00%00%00%00%00%20%D9%C9%11%D0%C0%40%AF%3F%EE%EE%92%94%D6%16X%B5%BCMH%15%2F%BF%D4%A7%C87%F1%8E%F2%81%AE%AAvzr%DA2%ABV%17%7C%E63%83%E7I%DC%C6%0Bs%1B%EF6%1E%00%00%00%00%00%00%00%00%00%80cr%9CW%FF%7F%C6%01%0E%F1%CE%A5%84%B3%CA%BC%E0%CB%AA%84%CE%F9%BF)%EC%13%08WU%AE%AB%B1%AE%2BO%EC%8E%CBYe%FE%8CN%ABr%5Dy%60~%CFA%0D%F4%AE%D4%BE%C75%CA%EDVB%EA(%B7%F1%09g%E5%D9%12%00%00%00%00%00%00%00%00%00H%F6%EB%13S%E7y%5E%5E%FB%98%F0%22%D1%B2'%A7%F0%92%B1%BC%24z3%AC%7Dm%60%D5%92%B4%7CEUO%5E%F0%AA*%3BU%B9%AE%3E%A0j%94%07%A0%C7%A0%AB%FD%B5%3F%A0%F7%03T%3Dy%D7%F7%D6%D4%C0%AAU%D2%E6%DFt%3F%A8%CC%AA%F2%86%B9%D7%F5%1F%18%E6%01%F8%CC%D5%9E%F0%F3z%88%AA%90%EF%20%00%00%00%00%00%00%00%00%00%C0%A6%D3%EA%CFi%AFb%2C%7BB%0A%2B%C3%1A%D7%06V%D5%07%A8r%5D%3D%D9%A6%CAu%F5%25%CF%A2%99%97zNX%60%95%AB%5DUZ%D5%FBR%03%AB%1C%D4k%9F%3F%BB%5C%FF%81a%AE%AB'%7F%F3%EA%FE%F3z%94%AA%D8%DF%5B%01%00%00%00%00%00%00%00%00%00%8E%FB%F3%F2%B1%1B%8DWU%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*UiU%C7%BBe%E7%F3%B9%CB%AAJ%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5J%95*U%AAT%A9R%A5*%AAj%FD%C6%D4%5Eo%90%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5%86%AF%1B%9F%98%DA%EBm%BBV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%ADV%AB%D5j%B5Z%AD%D6%E4%F58%01%00%00%00%00%00%00%00%00%00%00%00%00%00%40%85%7F%02%0C%008%C2%D0H%16j%8FX%00%00%00%00IEND%AEB%60%82") repeat-x !important;
            border-top: 1px solid #BBBBBB !important;
            border-right: 1px dashed #BBBBBB !important;
            border-bottom: 1px solid #000000 !important;
        }

        .firebugRulerV {
            top: 0 !important;
            left: -15px !important;
            width: 14px !important;
            height: 100% !important;
            background: url("data:image/png,%89PNG%0D%0A%1A%0A%00%00%00%0DIHDR%00%00%00%0E%00%00%13%88%08%02%00%00%00%0E%F5%CB%10%00%00%00%04gAMA%00%00%D6%D8%D4OX2%00%00%00%19tEXtSoftware%00Adobe%20ImageReadyq%C9e%3C%00%00%06~IDATx%DA%EC%DD%D1v%A20%14%40Qt%F1%FF%FF%E4%97%D9%07%3BT%19%92%DC%40(%90%EEy%9A5%CB%B6%E8%F6%9Ac%A4%CC0%84%FF%DC%9E%CF%E7%E3%F1%88%DE4%F8%5D%C7%9F%2F%BA%DD%5E%7FI%7D%F18%DDn%BA%C5%FB%DF%97%BFk%F2%10%FF%FD%B4%F2M%A7%FB%FD%FD%B3%22%07p%8F%3F%AE%E3%F4S%8A%8F%40%EEq%9D%BE8D%F0%0EY%A1Uq%B7%EA%1F%81%88V%E8X%3F%B4%CEy%B7h%D1%A2E%EBohU%FC%D9%AF2fO%8BBeD%BE%F7X%0C%97%A4%D6b7%2Ck%A5%12%E3%9B%60v%B7r%C7%1AI%8C%BD%2B%23r%00c0%B2v%9B%AD%CA%26%0C%1Ek%05A%FD%93%D0%2B%A1u%8B%16-%95q%5Ce%DCSO%8E%E4M%23%8B%F7%C2%FE%40%BB%BD%8C%FC%8A%B5V%EBu%40%F9%3B%A72%FA%AE%8C%D4%01%CC%B5%DA%13%9CB%AB%E2I%18%24%B0n%A9%0CZ*Ce%9C%A22%8E%D8NJ%1E%EB%FF%8F%AE%CAP%19*%C3%BAEKe%AC%D1%AAX%8C*%DEH%8F%C5W%A1e%AD%D4%B7%5C%5B%19%C5%DB%0D%EF%9F%19%1D%7B%5E%86%BD%0C%95%A12%AC%5B*%83%96%CAP%19%F62T%86%CAP%19*%83%96%CA%B8Xe%BC%FE)T%19%A1%17xg%7F%DA%CBP%19*%C3%BA%A52T%86%CAP%19%F62T%86%CA%B0n%A9%0CZ%1DV%C6%3D%F3%FCH%DE%B4%B8~%7F%5CZc%F1%D6%1F%AF%84%F9%0F6%E6%EBVt9%0E~%BEr%AF%23%B0%97%A12T%86%CAP%19%B4T%86%CA%B8Re%D8%CBP%19*%C3%BA%A52huX%19%AE%CA%E5%BC%0C%7B%19*CeX%B7h%A9%0C%95%E1%BC%0C%7B%19*CeX%B7T%06%AD%CB%5E%95%2B%BF.%8F%C5%97%D5%E4%7B%EE%82%D6%FB%CF-%9C%FD%B9%CF%3By%7B%19%F62T%86%CA%B0n%D1R%19*%A3%D3%CA%B0%97%A12T%86uKe%D0%EA%B02*%3F1%99%5DB%2B%A4%B5%F8%3A%7C%BA%2B%8Co%7D%5C%EDe%A8%0C%95a%DDR%19%B4T%C66%82fA%B2%ED%DA%9FC%FC%17GZ%06%C9%E1%B3%E5%2C%1A%9FoiB%EB%96%CA%A0%D5qe4%7B%7D%FD%85%F7%5B%ED_%E0s%07%F0k%951%ECr%0D%B5C%D7-g%D1%A8%0C%EB%96%CA%A0%A52T%C6)*%C3%5E%86%CAP%19%D6-%95A%EB*%95q%F8%BB%E3%F9%AB%F6%E21%ACZ%B7%22%B7%9B%3F%02%85%CB%A2%5B%B7%BA%5E%B7%9C%97%E1%BC%0C%EB%16-%95%A12z%AC%0C%BFc%A22T%86uKe%D0%EA%B02V%DD%AD%8A%2B%8CWhe%5E%AF%CF%F5%3B%26%CE%CBh%5C%19%CE%CB%B0%F3%A4%095%A1%CAP%19*Ce%A8%0C%3BO*Ce%A8%0C%95%A12%3A%AD%8C%0A%82%7B%F0v%1F%2FD%A9%5B%9F%EE%EA%26%AF%03%CA%DF9%7B%19*Ce%A8%0C%95%A12T%86%CA%B8Ze%D8%CBP%19*Ce%A8%0C%95%D1ae%EC%F7%89I%E1%B4%D7M%D7P%8BjU%5C%BB%3E%F2%20%D8%CBP%19*Ce%A8%0C%95%A12T%C6%D5*%C3%5E%86%CAP%19*Ce%B4O%07%7B%F0W%7Bw%1C%7C%1A%8C%B3%3B%D1%EE%AA%5C%D6-%EBV%83%80%5E%D0%CA%10%5CU%2BD%E07YU%86%CAP%19*%E3%9A%95%91%D9%A0%C8%AD%5B%EDv%9E%82%FFKOee%E4%8FUe%A8%0C%95%A12T%C6%1F%A9%8C%C8%3D%5B%A5%15%FD%14%22r%E7B%9F%17l%F8%BF%ED%EAf%2B%7F%CF%ECe%D8%CBP%19*Ce%A8%0C%95%E1%93~%7B%19%F62T%86%CAP%19*Ce%A8%0C%E7%13%DA%CBP%19*Ce%A8%0CZf%8B%16-Z%B4h%D1R%19f%8B%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1%A2%A52%CC%16-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2EKe%98-Z%B4h%D1%A2EKe%D02%5B%B4h%D1%A2EKe%D02%5B%B4h%D1%A2E%8B%96%CA0%5B%B4h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%16-%95a%B6h%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-Z*%C3l%D1%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z%B4T%86%D9%A2E%8B%16-Z%B4T%06-%B3E%8B%16-Z%B4T%06-%B3E%8B%16-Z%B4h%A9%0C%B3E%8B%16-Z%B4h%A9%0CZf%8B%16-Z%B4h%A9%0CZf%8B%16-Z%B4h%D1R%19f%8B%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1%A2%A52%CC%16-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2EKe%98-Z%B4h%D1%A2EKe%D02%5B%B4h%D1%A2EKe%D02%5B%B4h%D1%A2E%8B%96%CA0%5B%B4h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%16-%95a%B6h%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-Z*%C3l%D1%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z%B4T%86%D9%A2E%8B%16-Z%B4T%06-%B3E%8B%16-Z%B4T%06-%B3E%8B%16-Z%B4h%A9%0C%B3E%8B%16-Z%B4h%A9%0CZf%8B%16-Z%B4h%A9%0CZf%8B%16-Z%B4h%D1R%19f%8B%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1R%19%B4%CC%16-Z%B4h%D1%A2%A52%CC%16-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2%A52h%99-Z%B4h%D1%A2EKe%98-Z%B4h%D1%A2EKe%D02%5B%B4h%D1%A2EKe%D02%5B%B4h%D1%A2E%8B%96%CA0%5B%B4h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%96%CA%A0e%B6h%D1%A2E%8B%16-%95a%B6h%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-%95A%CBl%D1%A2E%8B%16-Z*%C3l%D1%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z*%83%96%D9%A2E%8B%16-Z%B4T%86%D9%A2E%8B%16-Z%B4T%06-%B3E%8B%16-Z%B4%AE%A4%F5%25%C0%00%DE%BF%5C'%0F%DA%B8q%00%00%00%00IEND%AEB%60%82") repeat-y !important;
            border-left: 1px solid #BBBBBB !important;
            border-right: 1px solid #000000 !important;
            border-bottom: 1px dashed #BBBBBB !important;
        }

        .overflowRulerX > .firebugRulerV {
            left: 0 !important;
        }

        .overflowRulerY > .firebugRulerH {
            top: 0 !important;
        }

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        .fbProxyElement {
            position: fixed !important;
            pointer-events: auto !important;
        }
    </style>
</head> 
<body onselectstart="return false"> 

<!-- 顶部guang gao -->
<!-- <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2067456131&site=qq&menu=yes">
<img src="http://yyygcms.cn/ad.gif">
</a> -->
    <!-- <?php echo ($guanggao[content]); ?> -->

    <!-- 左侧悬浮 -->
    <!-- <ul style="left: 186.5px;" class="y-fixed-divs">

        <li><i style="background-position: -22px <?php echo $lou+6;$lou=$lou+6-15; ?>px;"></i><em>
                热门推荐</em></li>

        <?php if(is_array($daodao)): foreach($daodao as $key=>$mem): ?><li><i style="background-position: -22px <?php echo $lou;$lou=$lou-17; ?>px;"></i><em>
                    <?php  $namename=mb_substr($daodao[$louname][name],0,4,'utf-8'); echo $namename; $louname++; ?></em></li><?php endforeach; endif; ?>
        <li><i style="background-position: -22px <?php echo $lou;$lou=$lou-15; ?>px;"></i><em>
                云购社区</em><b></b></li>					 
    </ul>  -->
    <!-- 左侧悬浮 end-->   
    <!-- 导航   start  --> 
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /> 
<link rel="stylesheet" type="text/css" href="/Public/style/css/comm.css" /> 
<link rel="stylesheet" type="text/css" href="/Public/style/css/footer_header.css" /> 
<link rel="stylesheet" type="text/css" href="/Public/style/css/index.css" /> 
<link rel="stylesheet" type="text/css" href="/Public/style/css/new_join.css" /> 
<style>
    .pullDownList,.yMenuListCon{
        display:none
    }
</style> 
<!-- 顶部 --> 
<input type="hidden" id="mid" value="" /> 
<input type="hidden" id="signTime" value="" /> 
<input type="hidden" id="signDays" value="" /> 
<!-- 2015-5-22 -修改 start  .header增加header_fixed类 --> 
<div class="header header_fixed"> 
    <div class="header1"> 
        <div class="header1in"> 
            <!--<ul class="headerul1"> -->
                <!--<li><a style="padding-left:40px;font-size: 14px;"><i class="header-tel"></i><?php echo C('cell');?></a></li> -->
                <!--&lt;!&ndash;<li class="hreder-hover" style="border-right:none;"><a href="http://wpa.qq.com/msgrd?V=1&uin=<?php echo C('qq');?>&Menu=yes" target="_blank">在线客服</a></li> &ndash;&gt;-->
                <!--&lt;!&ndash;<li class="phoneli header-WeChatli"> <a  style=" padding-bottom: 0px;">关注我们<i class="i-header-WeChat"></i></a> <img src="/Public/uploads/<?php echo C('web_logo1');?>" /> </li> &ndash;&gt;-->
                <!--&lt;!&ndash;<li class="phoneli header-phoneli"> <a href="https://www.pgyer.com/dzmM" style="border-right:0px;">手机客户端<i class="i-header-phone"></i></a>&ndash;&gt;-->
                    <!--&lt;!&ndash; /footer/app_client.html &ndash;&gt; -->
                    <!--&lt;!&ndash; <img src="/Public/style/images/weixinlogo.png"> &ndash;&gt; </li> -->
            <!--</ul> -->
            <ul class="headerul2">
                <!--<li><a href="https://www.pgyer.com/dzmM">APP</a></li>
                <li><a href="/user/qiandao">签到</a></li>-->

                <?php if(($huiyuan)): ?><li><a href="/user/home"><?php echo R("base/huode_user_name",array($huiyuan,"username"));?></a></li>
                    <li><a href="/user/cook_end"><?php echo (L("web_user_back")); ?></a></li>

                    <?php else: ?>t h l
                    <li><a href="/user/login"><?php echo (L("web_user_login")); ?></a></li>
                    <li><a href="/user/register"><?php echo (L("web_user_register")); ?></a></li><?php endif; ?>
                <li class="MyzhLi"> <a href="/user/home"><?php echo (L("web_user_me")); echo C('web_name_two');?><i class="top"></i></a>
                    <dl class="Myzh"> 
                        <dd>
                            <a href="/user/userbuylist"><?php echo (L("web_user_purchase")); ?></a>
                        </dd> 
                        <dd>
                            <a href="/user/orderlist"><?php echo (L("web_user_order_list")); ?></a>
                        </dd> 
                        <dd>
                            <a href="/user/userModify"><?php echo (L("web_user_user_modify")); ?></a>
                        </dd> 
                        <!--<dd>-->
                            <!--<a href="/user/wangpan">我的网盘</a>-->
                        <!--</dd> -->
                    </dl> </li>
                <li><a href="/user/userrecharge"><?php echo (L("web_user_user_recharge")); ?></a></li>
                <li><a href="/index/about"><?php echo (L("web_user_about")); ?></a></li>
                <!--<li><a style="border-right:none;" href="/index/group_qq">官方QQ群</a></li> -->
            </ul> 
        </div> 
    </div> 
    <div class="header2"> 
    	<!-- logo -->
        <a href="/index/index" class="header_logo"><img src="/Public/uploads/<?php echo R('base/Getlogo',array());?>" /></a> 

		<!-- 随机出现 -->
        <div class="yJoinNum" style='margin-left:190px'>
            <input type="hidden" value="<?php echo R('base/go_count_renci',array());?>">
            <a href="/index/buyrecord" target="_blank" class="allNums">
                <span class="yBefore"><?php echo (L("web_user_has")); ?></span>
                <span class="yNumList">
                    <ul style="margin-top: -270px;">
                        <li t="9">9</li>
                        <li t="8">8</li>
                        <li t="7">7</li>
                        <li t="6">6</li>
                        <li t="5">5</li>
                        <li t="4">4</li>
                        <li t="3">3</li>
                        <li t="2">2</li>
                        <li t="1">1</li>
                        <li t="0">0</li>
                    </ul>
                </span>
                <span class="w_ci_bg"></span>
                <span><?php echo (L("web_user_part_in")); ?></span>
            </a>
        </div>


        <!-- 2015 6 9 start--> 
        <div class="search_header2"> 
            <s></s>
            <input type="text" placeholder=<?php echo (L("web_user_search_has")); ?> value="" id="q" />
            <a href="javascript:;" class="btnHSearch"><?php echo (L("web_user_search")); ?></a>
            <span class="search_span_a">
                <a href="/index/s_tag/val/<?php echo (L("web_user_phone")); ?>"><?php echo (L("web_user_phone")); ?></a>
                <a href="/index/s_tag/val/<?php echo (L("web_user_computer")); ?>"><?php echo (L("web_user_computer")); ?></a>
                <a href="/index/s_tag/val/<?php echo (L("web_user_notebook")); ?>"><?php echo (L("web_user_notebook")); ?></a>
            </span>
        </div> 


        <!-- 2015 6 9 end--> 
    </div> 
</div> 

<div style="clear:both;"></div>
<!-- 导航   start  -->
<div class="yNavIndexOut">
    <div class="yNavIndex">
        <div class="pullDown">
            <h4 class="pullDownTitle">
                <a target="" href="/goods/glist"><?php echo (L("web_user_classification")); ?></a>
            </h4>
            <ul class="pullDownList" style="<?php if(($isindex == 'Y')): ?>display:block;overflow: hidden;<?php else: ?>display:none;<?php endif; ?>">
                <?php if(is_array($daodao)): foreach($daodao as $key=>$categoryx): ?><li class="">
                        <i class="listi<?php echo $i;$i++; ?>"></i>
                        <a href="/goods/glist/type/<?php echo ($categoryx['cateid']); ?>.html"><?php echo ($categoryx['name']); ?></a>
                        <span></span>
                    </li><?php endforeach; endif; ?>
            </ul>
            <div class="yMenuListCon" style="display: none;">
                <?php if(is_array($daodao)): foreach($daodao as $key=>$categoryx): ?><div class="yMenuListConin" style="display: none;">
                        <?php $data=D("fenlei")->where(array("model"=>1,"parentid"=>$categoryx['cateid']))->order("`order` DESC")->select(); ?>
                        <?php if(is_array($data)): foreach($data as $key=>$categoryy): ?><div class="yMenuLCinList">
                                <h3>
                                    <a class="yListName" href="/goods/glist/type/<?php echo ($categoryy['cateid']); ?>.html"><?php echo ($categoryy['name']); ?></a>
                                    <a class="yListMore" href="/goods/glist/type/<?php echo ($categoryy['cateid']); ?>.html"><?php echo (L("web_user_more")); ?></a>
                                </h3>
                                <p>
                                <?php if(is_array($pinpin)): foreach($pinpin as $key=>$vo): $cateid=explode(',',$vo['cateid']); if(in_array($categoryy['cateid'],$cateid)){ ?>
                                    <a href="/goods/glist/type/<?php echo ($categoryy['cateid']); ?>_<?php echo ($vo['id']); ?>_0.html"><?php echo ($vo['name']); ?></a>
                                    <?php } endforeach; endif; ?>
                                </p>
                            </div><?php endforeach; endif; ?>

                    </div><?php endforeach; endif; ?>


            </div>
        </div>
        <ul class="yMenuIndex">
            <?php echo R('base/Getheader',array('index'));?>
            <li class="hide-menu-nav" style="padding: 0 13px 0px 15px;">
                <span></span>
                <a class="hide-menu-nava" href="javascript:void(0)"><?php echo (L("web_user_find")); ?></a>
                <dl>
                    <?php echo R('base/Getheader',array('faxian'));?>
                </dl>
            </li>


        </ul> 
    </div> 
</div> 
<!-- 导航   end  --> 
<!-- 右侧悬浮 --> 
<!-- 滑过右侧购物车时未登录时的登陆框 --> 

<!-- 滑过右侧购物车时登录后的列表 --> 
<div class="Left-fixed-divs2 Left-fixed-divs3">
    <!-- 无商品时 --> 
    <p id="noCart" class="yNocommodity"><?php echo (L("web_user_shopping_bag")); ?></p>
    <!-- 购物袋有商品 start --> 
    <dl id="list"> 
    </dl> web_user_shopping
    <div class="fixed-divbottom">
        <p><?php echo (L("web_user_select")); ?><span id="row">0</span><?php echo (L("web_user_select")); ?><span class="yflr"><?php echo (L("web_lottery_mark")); ?><em id="hpriceTotal">0</em><?php echo (L("web_user_money")); ?></span></p>
        <a href="/goods/cartlist"><?php echo (L("web_user_account")); ?></a>
    </div> 
</div> 
<!-- 购物袋有商品 end --> 
<div class="Left-fixed-divs" style="display: none"> 
    <!--<img src="/Public/style/images/ce.png" style="top:5%;position:relative;">-->
    <ul> 
        <li class="shoppingCartRightFix Left-fixed-divs3"> <a href="/goods/cartlist"> <i></i> <em><?php echo (L("web_user_gou")); ?></em> <em><?php echo (L("web_user_wu")); ?></em> <em><?php echo (L("web_user_dai")); ?></em> <em id="cartCount"></em> </a> </li>
        <!--<li class="YonlineService otherlifix"><a hidefocus="true" href="http://wpa.qq.com/msgrd?V=1&uin=<?php echo C('qq');?>&Menu=yes" target="_blank"><i style="margin-left:12px;"></i><em>在线</em><em>客服</em></a></li> -->
        <!--<li class="otherlifix otherlifixw"> <a hidefocus="true" href="javascript:void 0"> <i style="background-position:-168px -176px;"></i> <em>官方</em> <em>微信</em> </a> <img width="188" height="216" src="/Public/uploads/<?php echo C('web_logo1');?>" /> -->
            <!--<s></s> </li> -->
        <!--<li class="otherlifix otherlifixw"> <a hidefocus="true" href="https://www.pgyer.com/dzmM" target="_blank"> <i style="margin-left:12px;margin-top:0;background-position:-203px -172px;height: 27px;margin-bottom:0;"></i> <em>手机</em><em>App</em> </a> -->
            <!--&lt;!&ndash; <img width="188" height="188" src="/Public/style/images/weixinlogo.png" style="border:1px solid #adadad;"> &ndash;&gt; </li> -->
        <!--<li class="otherlifix"> <a hidefocus="true" href="/user/userrecharge"> <i style="margin-left:12px;background-position:-236px -176px;"></i> <em>快速</em><em>充值</em> </a> </li> -->
        <li class="otherlifix lifixTop"> <i style="background-position:-276px -170px"></i> <em>$Think.lang.web_user_zhi}</em><em>$Think.lang.web_user_ding}</em> </li>
    </ul> 
</div> 
<!-- 右侧悬浮 end --> 
<script type="text/javascript" src="/Public/style/js/common.js"></script>
<script type="text/javascript" src="/Public/style/js/jquery.js"></script>
<script type="text/javascript" src="/Public/style/js/jquery.lazyload.min.js"></script>
<script type="text/javascript" src="/Public/style/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/Public/style/js/jquery.cookies.2.2.0.js"></script>
<script type="text/javascript" src="/Public/style/js/footer_header.js"></script>
<script>

    $(function () {
        <?php if(($isindex == 'Y')): ?>var index = $("#index").html();
        if (index == null)
                <?php else: ?>
                var index = $("#index").html();
        if (index == index)<?php endif; ?>

        {
            $(".pullDownTitle").mouseover(function () {
                $(".pullDown").hover(function () {
                    $(".pullDownList").show();
                }, function () {
                    $(".pullDownList").hide();
                });
            })
        }
        $(".btnHSearch").click(function () {
            location.href = '/index/s_tag/val/' + $("#q").val();
        });
        cartCount();
    });
    var roots = ""; var pub = "/Public";

</script> 

<span id="index" style="display:none">index</span> 
<!-- 导航   end  --> 
<!-- 右侧悬浮 -->
<!-- 滑过右侧购物车时未登录时的登陆框 -->
<div style="top: 123px;" class="Left-fixed-divs3 Left-fixed-login">
	<span class="close-login"></span>
	<span class="right-login-xs"></span>
	
</div>



<input value="652" id="gid" type="hidden"> 
<input value="1" id="pid" type="hidden">
<div class="w_con" id="goods_details" style="">
	<div class="w_details_left">
		<h4 class="w_guide"><a href="/index/index">首页</a>
		<a href="/goods/glist/">全部商品</a><a href="/goods/glist/type/<?php echo ($xiangmu['cateid']); ?>.html"><?php echo ($fenlei['name']); ?></a><a href="/goods/glist/type/<?php echo ($xiangmu['cateid']); ?>e<?php echo ($xiangmu['brandid']); ?>.html"><?php echo ($pinpai['name']); ?></a><a class="w_accord" href="javascript:void%200">商品详情</a></h4>
	  
	  








	  <link rel="stylesheet" type="text/css" href="/Public/style/css/pager2.css">
	  
	 <!--显示揭晓动画 start-->
			<style type="text/css">

    .w_winner_bg b{ float:left; padding:1px 5px 5px 0;  margin:0px;word-break: keep-all;overflow:hidden;}

</style>

<div class="w_details_top">     <div class="w_details_choose">         

        <dl class="w_big_img">   
            <?php if(is_array($xiangmu['picarr'])): foreach($xiangmu['picarr'] as $key=>$imgtu): ?><dd style="display: block;"><img style="display: inline;" src="/Public/uploads/<?php echo ($imgtu); ?>" class="lazy400" data-original="/Public/uploads/<?php echo ($imgtu); ?>"><noscript><img src="/Public/uploads/<?php echo ($xiangmu['picarr'][0]); ?>" /></noscript></dd><?php endforeach; endif; ?>    


        </dl>        
        <ul class="w_small_img">
            <i class="w_modified" style="left: 38px;"></i>
            <div id="imageMenu" >
                <?php  $ttt=1; ?>
                <?php if(is_array($xiangmu['picarr'])): foreach($xiangmu['picarr'] as $key=>$imgtu): ?><li class="" style="display: <?php  if ($ttt>5){echo none;}$ttt++; ?>;"><img style="display: inline;" src="/Public/uploads/<?php echo ($imgtu); ?>" class="lazy54" data-original="/Public/uploads/<?php echo ($imgtu); ?>"><noscript><img src="/Public/uploads/<?php echo ($imgtu); ?>" /></noscript></li><?php endforeach; endif; ?>         
            </div>  

        </ul>     </div>     <div class="w_details_text">         <div class="baoyuan" >                                      <dl class="w_rob w_rob_another">             <!-- 2015-5-22 - 新增类  class="w_slip_out" -->             <!-- 2015-6-11 修改 start -->                                        <!-- 2015-6-11 修改 end -->                <div class="w_clear"></div>            </dl>   </div>   <div class="zhengchang">    <!-- 正常购买 -->    <p><strong><c id="cart_title"><?php echo ($xiangmu['title']); ?><span style="color:red"><?php echo ($xiangmu['title2']); ?></span></c></strong><i></i></p>          <input value="1" id="cart_priceArea" type="hidden">          <b>价值：￥<c id="cart_priceTotal"><?php echo ($xiangmu['yunjiage']); ?></c></b>                 <ul class="w_number">              <li class="w_amount w_amount_one" id="cart_priceSell"><?php echo ($xiangmu['canyurenshu']); ?></li>              <li class="w_amount" id="cart_need"></li>              <li class="w_amount  w_amount_two w_amount_val" id="cart_surplus"><?php echo ($nomal); ?></li>              <li class="w_amount_one">已出售</li>              <li>  </li>              <li class="w_amount_two">库存数量</li>          </ul>          <div class="w_cumulative w_cumulative_another">              <strong>购买：</strong>              <input class="w_detailsinputs w_detailsinputs_one times num_dig"  id="num_dig" value="1" min="1" max="<?php echo ($xiangmu['shenyurenshu']); ?>" maxlength="7" type="text">              <span class="w_subtracts_one"></span>              <span class="w_pluss_one"></span>                        <strong class="w_tail">件</strong>          <div class="y-hide-span">                                </div>          </div>                    <dl class="w_rob w_rob_another">              <dd>

					<?php if($xiangmu['zongrenshu']-$xiangmu['canyurenshu'] <= 0): ?><a href="/index/index" class="orangeBtn">商品已售完</a>
                    <?php else: ?>
                    <a class="w_slip Det_Shopbut" data-gid="<?php echo ($xiangmu['id']); ?>" data-pid="" href="javascript:;">我要全额购</a><?php endif; ?>

                </dd>                                           <div class="w_clear"></div>          </dl>       </div>         <ul class="w_security">            <li class="w_security_one">公平公正公开</li>            <li class="w_security_two">品质保障</li>            <li class="w_security_three">全国免运费（港澳除外）</li>            <li class="w_security_four">权益保障</li>            <div class="w_clear"></div>         </ul>               <!--您的本期潮购码结束-->  

			<?php if(($xiangmu['shenyurenshu']=='0') and ($xiangmu['xsjx_time']=='0') and (empty($item['q_uid']))): ?><div class="Immediate">
            <span style="left:10px;right:0px;">这个商品已购买完！</span> 
        </div><?php endif; ?>
     <?php
 $uuiid=R('base/huode_user_uid'); $record=D('yonghu_yys_record')->where(array('shopid'=>$xiangmu[id],'shopqishu'=>$xiangmu[qishu],'uid'=>$uuiid))->find(); ?>
	 <?php if(R('base/huode_user_uid')): else: ?>
        <!--未参加潮购开始-->  

        <div class="w_winner_bg">            
            <span class="w_not_join"><a href="/member/user/login" >看不到？是不是没登录或是没注册？ 登录后看看</a></span> 

            <strong></strong>        
        </div><?php endif; ?>
        <!--未登录结束-->         
        <div data-bd-bind="1440953827063" class="bdsharebuttonbox bdshare-button-style0-16">    <a href="#" class="bds_more" data-cmd="more"></a>    <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>    <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>    <a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>    <a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>    <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>   </div>             </div> </div>   </div>
<!--揭晓前-->
<?php if($zongji > 2): ?><div class="w_clear"></div>
<?php else: ?>
<!--揭晓前-->
<div class="w_details_right"><h3>揭晓信息</h3><div class="w_time_backward w_time_backward_other"><h6>谁会是本期幸运儿</h6><p class="w_backward_other"></p><div class="w_boy_other"></div></div><p class="w_deng">敬请期待...<br>商品二维码带佣金</p><br>	

   <?php if(R('base/huode_user_uid')): ?><img src=/Invite/sperweima/id/<?php echo ($xiangmu['id']); ?> width=160 height=160> <?php else: ?><div class="w_deng">请登陆后获取</div><?php endif; ?></div>    
<div class="w_clear"></div>


<!--揭晓后结束--><?php endif; ?>
<div class="w_details_bottom">
    <!--最新上架-->

    <div class="w_shelves">
        <div class="w_shelves_top">
            <h3>最新上架</h3>

            <ul class="w_shelves_one">   
                  <?php  $yyslistrenqib= D("shangpin")->where("q_uid is null")->order("q_counttime DESC")->limit("4")->select(); ?>
               <?php if(is_array($yyslistrenqib)): foreach($yyslistrenqib as $key=>$zuixin): ?><li>   <a href="/goods/items/goodsId/<?php echo ($zuixin['id']); ?>.html">    <img style="display: inline;" src="/Public/uploads/<?php echo ($zuixin['thumb']); ?>" class="lazy200" data-original="/Public/uploads/<?php echo ($zuixin['thumb']); ?>">    <noscript><img src="/Public/uploads/<?php echo ($zuixin['thumb']); ?>" alt=""/></noscript>   </a>  </li>   <b>总需人次：<?php echo ($zuixin['zongrenshu']); ?>人次</b>   <a href="/goods/items/goodsId/<?php echo ($zuixin['id']); ?>.html"><?php echo ($zuixin['title']); ?></a> </ul>  <ul class="w_shelves_one"><?php endforeach; endif; ?>
            </ul>  </div>
        <div class="w_shelves_bottom">
            <h3>最新参与记录</h3>

            <div class="w_record_out"> 

                <?php  $yyslistrenqib= D("shangpin")->where("renqi='1' and q_uid is null")->order("q_counttime DESC")->limit("5")->select(); ?>
                 <?php if(is_array($yyslistrenqib)): foreach($yyslistrenqib as $key=>$renqi): ?><div class="w_record_in">  <div class="w_record">    <div class="w_record_img">   <img style="display: inline;" src="/Public/uploads/<?php echo ($renqi['thumb']); ?>" class="lazy54" data-original="/Public/uploads/<?php echo ($renqi['thumb']); ?>">   <noscript><img src="/Public/uploads/<?php echo ($renqi['thumb']); ?>" /></noscript>  </div>    <p class="w_record_con" style="word-break: break-all; word-wrap:break-word;"><a href="/goods/items/goodsId/<?php echo ($renqi['id']); ?>.html"><?php echo ($renqi['title']); ?> </a><span><br>剩余<?php echo ($renqi['zongrenshu']-$renqi['canyurenshu']); ?>人次</span></p>    <div class="w_clear"></div> </div>        </div><?php endforeach; endif; ?>
            </div>

        </div>
    </div>
    <!--奖品详情-->
    <div class="w_prize pgp ">
        <dl class="w_calculate_nav">
            <dd class="w_results_arrow">商品详情</dd>
            <dd>所有参与记录</dd>
            <dd>晒单</dd>
            <span class="w_remaining"></span>
        </dl>
        <div class="w_calculate_con">
            <div class="w_prize_con w_prize_img" style="display: block;">
                <?php echo ($xiangmu['content']); ?>
            </div>
            <!--所有参与记录-->



            <div class="w_calculate_one w_calculate_two ">
                <div class="w_clear"></div>





                <table class="w_yun_con" cellpadding="0" cellspacing="0">
                    <iframe src="/goods/go_record_ifram/id/<?php echo ($xiangmuid); ?>/len/20" style="width:978px; border:block;height:1250px" frameborder="0" scrolling="no"></iframe>	
                </table>
                <div id="kkpager2" style="margin: 64px auto;"></div>
                <div class="w-msgbox m-detail-codesDetail" id="pro-view-9" style="z-index:10000;">
                    <a data-pro="close" href="javascript:void(0);" class="w-msgbox-close"></a>	
                    <div class="w-msgbox-hd" data-pro="header">
                        <h3></h3>
                    </div>
                    <div class="w-msgbox-bd" data-pro="entry">
                        <div class="m-detail-codesDetail-bd">
                            <div class="m-detail-codesDetail-wrap">
                                <dl class="m-detail-codesDetail-list f-clear">
                                </dl>
                            </div>
                        </div>
                    </div>	
                </div>
                <script type="text/javascript" src="/Public/style/js/pager2.js"></script>
                <script type="text/javascript" src="/Public/style/js/timeline.js"></script>

            </div>
            <!--晒单-->
            <div class="w_calculate_one">


                <link rel="stylesheet" type="text/css" href="/Public/style/css/pager1.css">



                <div class="sun"></div>
                <div  style="margin: 64px auto;">

                    
                    <div id="divPost" class="Single_Content">
                		
                        <div class="Single_list">
                            
                            
                        </div>
                        </foreach>
                         <?php if($zongji > $num): ?><div class="pagesx">{page:two}</div><?php endif; ?>
                    </div>
                    <else>
                    <div style="text-align:center;width:100%;height:80px;line-height:80px;">
                        <h1 style='text-align:center;width:100%;font-size:22px; font-weight:bold;color:#555;'>该商品还未有晒单！</h1>
                    </div>
                          </if>



                </div>


	
				
			<!--显示揭晓动画 end-->	







	
				
<script type="text/javascript" src="/Public/style/js/pager1.js"></script>
<script type="text/javascript" src="/Public/style/js/goodsShow.js"></script>

				</div>
			</div>
		</div>
		<div class="w_clear"></div>
	</div>
</div>

<!-- 云购码弹窗 -->
<div class="w-msgbox m-detail-codesDetail" id="pro-view-7" style="z-index: 10000; left: 484.5px; top: 197.5px;">
	<a data-pro="close" href="javascript:closeCodesPane();" class="w-msgbox-close"></a>	
	<div class="w-msgbox-hd" data-pro="header">
		<h3>您本期总共参与了<span class="txt-red"></span>人次</h3>
	</div>
	<div class="w-msgbox-bd" data-pro="entry">
		<div class="m-detail-codesDetail-bd">
			<div class="m-detail-codesDetail-wrap">
				<dl class="m-detail-codesDetail-list f-clear">
				</dl>
			</div>
		</div>
	</div>	
</div>
<!-- 提示是否继续购买 -->
<div id="cartMsg" class="c_msgbox_bj" style="height: 7369px; display: none; z-index: 9999;"></div>
<div style="left: 657px; top: 274px;" class="once_shop_con">
    <a class="w_decisive" href="javascript:gotoCart(1)"></a>
    <a class="w_consider" href="javascript:"></a> 
  </div>
<!-- 云购期数弹窗 start -->
<div class="w-msgbox m-detail-codesDetail" style="z-index: 10000; left: 484.5px; top: 207.5px;" id="pro-view-8">
  <a data-pro="close" href="javascript:void(0);" class="w-msgbox-close" id="w_close"></a>  
  <div class="w-msgbox-hd" data-pro="header">
    <h3><input id="p" type="text"><span class="w_jie_shu"><a href="#" onclick="toP()">揭晓期数</a></span></h3>
  </div>
  <div class="w-msgbox-out" data-pro="entry">
    <div class="m-detail-codesDetail-bd">
      <div class="m-detail-codesDetail-wrap m-detail-codesDetail-in">
        <dl class="m-detail-codesDetail-list f-clear m-detail-codesDetail-one">
          
        </dl>
      </div>
    </div>
  </div>  
</div>
<!-- 云购期数弹窗 end -->
<div class="c_msgbox_bj" style="z-index: 9999; height: 7369px;"></div>


<!-- 底部 -->

                             


<script type="text/javascript" src="/Public/style/js/c_cloud.js"></script>
<script type="text/javascript" src="/Public/style/js/goods_details_during.js"></script>
<script type="text/javascript">
$(function(){
	
});
setTimeout(function(){
	$(".yMenua").removeClass("yMenua");
},500);
</script>

<script type="text/javascript">
<!--补丁3.1.6_b.0.2-->
function set_iframe_height(fid,did,height){	
	$("#"+fid).css("height",height);	
}

$(function(){
	$("#ulRecordTab li").click(function(){
		var add=$("#ulRecordTab li").index(this);
		$("#ulRecordTab li").removeClass("Record_titCur").eq(add).addClass("Record_titCur");
		$(".Pro_Record .hide").hide().eq(add).show();
	});
	
	var DetailsT_TitP = $(".DetailsT_TitP ul li");
	var divContent    = $("#divContent div");	
	DetailsT_TitP.click(function(){
		var index = $(this).index();
			DetailsT_TitP.removeClass("DetailsTCur").eq(index).addClass("DetailsTCur");
	
			var iframe = divContent.hide().eq(index).find("iframe");
			if (typeof(iframe.attr("g_src")) != "undefined") {
			  	 iframe.attr("src",iframe.attr("g_src"));
				 iframe.removeAttr("g_src");
			}
			divContent.hide().eq(index).show();
	});
	<!--补丁3.1.6_b.0.2-->
	
	var fouli=$(".DetailsT_TitP ul li")
	$("#btnUserBuyMore").click(function(){
		 fouli.removeClass("DetailsTCur").eq(1).addClass("DetailsTCur");
		var iframe = divContent.hide().eq(1).find("iframe");
			if (typeof(iframe.attr("g_src")) != "undefined") {
			  	 iframe.attr("src",iframe.attr("g_src"));
				 iframe.removeAttr("g_src");
			}
		 $("#divContent .divContent-in").hide().eq(1).show();
		 $("html,body").animate({scrollTop:1280},1500);
		 $("#divProductNav").addClass("nav-fixed");
	});
	$(window).scroll(function(){
		if($(window).scrollTop()>=1200){
			$("#divProductNav").addClass("nav-fixed");
		}else if($(window).scrollTop()<1200){
			$("#divProductNav").removeClass("nav-fixed");
		}
	});
})
var shopinfo={shopid:<?php echo ($xiangmu['id']); ?>,money:<?php echo ($xiangmu['yunjiage']); ?>,shenyu:<?php echo ($nomal); ?>};

	
$(function(){
	function baifenshua(aa,n){
	n = n || 2;
	return ( Math.round( aa * Math.pow( 10, n + 2 ) ) / Math.pow( 10, n ) ).toFixed( n ) + '%';
}
	var shopnum = $("#num_dig");
	shopnum.keyup(function(){
		if(shopnum.val()><?php echo ($nomal); ?>){
			shopnum.val(<?php echo ($nomal); ?>);
		}
		var numshop=shopnum.val();
		if(numshop==<?php echo ($xiangmu['zongrenshu']); ?>){
			var baifenbi='100%';
		}else{
			var showbaifen=numshop/<?php echo ($xiangmu['zongrenshu']); ?>;
			var baifenbi=baifenshua(showbaifen,2);
		}
		$("#chance").html("<span style='color:red'>获得机率"+baifenbi+"</span>");
	});	
	
	$("#shopadd").click(function(){
		var shopnum = $("#num_dig");
			var resshopnump='';
			var num = parseInt(shopnum.val());				
			if(num+1 > <?php echo ($nomal); ?>){				
				shopnum.val(<?php echo ($nomal); ?>);
				resshopnump = <?php echo ($nomal); ?>;
			}else{
				resshopnump=parseInt(shopnum.val())+1;
				shopnum.val(resshopnump);	
				if(shopnum.val()>=<?php echo ($nomal); ?>){
					resshopnump=shopinfo['shenyu'];
					shopnum.val(resshopnump);
				}
			}
			if(resshopnump==<?php echo ($xiangmu['zongrenshu']); ?>){
				var baifenbi='100%';
			}else{
				var showbaifen=resshopnump/<?php echo ($xiangmu['zongrenshu']); ?>;
				var baifenbi=baifenshua(showbaifen,2);
			}
			$("#chance").html("<span style='color:red'>获得机率"+baifenbi+"</span>");
	});
	 $("li").click(function(){
    var yiyuansha=$(this).text();
     var renren=<?php echo ($xiangmu['shenyurenshu']); ?>;
    // alert(yiyuansha);
     //alert(renren);
     if(yiyuansha<renren){
    document.getElementById("num_dig").value=yiyuansha;	
   var shopnums=yiyuansha;
		if(shopnums==<?php echo ($xiangmu['zongrenshu']); ?>){
				var baifenbi='100%';
			}else{
				var showbaifen=shopnums/<?php echo ($xiangmu['zongrenshu']); ?>;
				var baifenbi=baifenshua(showbaifen,2);
			}
  $("#chance").html("<span style='color:red'>获得机率"+baifenbi+"</span>");
  }else{
  $("#chance").html("<span style='color:red'>购买人次太多哦</span>");
  }
})
	
	$("#shopsub").click(function(){
		var shopnum = $("#num_dig");
		var num = parseInt(shopnum.val());
		if(num<2){
			shopnum.val(1);			
		}else{
			shopnum.val(parseInt(shopnum.val())-1);
		}
		var shopnums=parseInt(shopnum.val());
		if(shopnums==<?php echo ($xiangmu['zongrenshu']); ?>){
				var baifenbi='100%';
			}else{
				var showbaifen=shopnums/<?php echo ($xiangmu['zongrenshu']); ?>;
				var baifenbi=baifenshua(showbaifen,2);
			}
			$("#chance").html("<span style='color:red'>获得机率"+baifenbi+"</span>");
	});
});

$(function(){
$(".Det_Cart").click(function(){ 
	//添加到购物车动画
	var src=$(".bigImg img").attr('src');  
	var $shadow = $('<img id="cart_dh" style="display: none; border:1px solid #aaa; z-index: 99999;" width="400" height="400" src="'+src+'" />').prependTo("body"); 
	var $img = $(".bigImg img");
	$shadow.css({ 
	   'width' : $img.css('width'), 
	   'height': $img.css('height'),
	   'position' : 'absolute',      
	   'top' : $img.offset().top,
	   'left' : $img.offset().left, 
	   'opacity' :1    
	}).show();
	var $gouwuche =$("#btnMyCart");
	var numdig=$(".num_dig").val();
	$shadow.animate({   
		width: 1, 
		height: 1, 
		top: $gouwuche.offset().top, 
		left: $gouwuche.offset().left,
		opacity: 0
	},500,function(){
		Cartcookie(false);
	});		
});
	$(".Det_Shopbut").click(function(){	
		Cartcookie(true);
	});	
});



function Cartcookie(cook){
	var shopid=shopinfo['shopid'];
	var number=parseInt($("#num_dig").val());
	if(number<=1){number=1;}
	var Cartlistzg = $.cookie('Cartlistzg');
	if(!Cartlistzg){
		var info = {};
	}else{
		var info = $.evalJSON(Cartlistzg);
		if((typeof info) !== 'object'){
			var info = {};
		}
	}		
	if(!info[shopid]){
		var CartTotal=$("#btnMyCart em").text();
			$(".yShoppingCart1 span").text(parseInt(CartTotal)+1);
			$("#btnMyCart em").text(parseInt(CartTotal)+1);
	}	
	info[shopid]={};
	info[shopid]['num']=number;
	info[shopid]['shenyu']=shopinfo['shenyu'];
	info[shopid]['money']=shopinfo['money'];
	info['MoenyCount']='0.00';	
	$.cookie('Cartlistzg',$.toJSON(info),{expires:7,path:'/'});
	if(cook){
		window.location.href="/goodszg/Cartlistzg/"+new Date().getTime();//+new Date().getTime()
	}
}  
</script> 


<style>

*{ margin:0; padding:0; list-style:none;}
img{ border:0;}

.rides-cs {  font-size: 12px;; position: fixed; top: 45%;  _position: absolute; z-index: 1500; border-radius:6px 0px 0 6px;}
.rides-cs a { }
.rides-cs a:hover {  text-decoration: none;}
.rides-cs .floatL { width: 36px; float:left; position: relative; z-index:1;margin-top: 21px;height: 181px;}
.rides-cs .floatL a { font-size:0; text-indent: -999em; display: block;}
.rides-cs .floatR { width: 130px; float: left; padding: 5px; overflow:hidden;}
.rides-cs .floatR .cn { border-radius:6px;margin-top:4px;}
.rides-cs .cn .titZx{ font-size: 14px; color: #333;font-weight:600; line-height:24px;padding:5px;text-align:center;}
.rides-cs .cn ul {padding:0px;}
.rides-cs .cn ul li { line-height: 38px; height:38px;border-bottom: solid 1px #E6E4E4;overflow: hidden;text-align:center;}
.rides-cs .cn ul li span { color: #777;}
.rides-cs .cn ul li a{color: #777;}
.rides-cs .cn ul li img { vertical-align: middle;}
.rides-cs .btnOpen, .rides-cs .btnCtn {  position: relative; z-index:9; top:25px; left: 0;  background-image: url(/index.png); background-repeat: no-repeat; display:block;  height: 146px; padding: 8px;}
.rides-cs .btnOpen { background-position: 0 0;}
.rides-cs .btnCtn { background-position: -37px 0;}
.rides-cs ul li.top { border-bottom: solid #ACE5F9 1px;}
.rides-cs ul li.bot { border-bottom: none;}
</style>




<!--<a href="javascript:;" id="launch_qq11" class="btn7 rides-cs">-->
<!--<img src=/index.png>-->
<!--</a>-->

<script charset="utf-8" src="http://wpa.b.qq.com/cgi/wpa.php"></script>
<script>
　　//自定义配置
　　BizQQWPA.addCustom({aty: '0', a: '0', nameAccount: '4000482466', selector: 'launch_qq11'});
</script>
<link rel="stylesheet" type="text/css" href="/Public/piyungou/css/Comm222.css"/>
<!-- 底部 --> 
 <div class="g-footer">
	<div class="m-instruction ">
		<div class="g-wrap f-clear">
			<div class="g-mainabc">
				<?php $number=1; $category=D("fenlei")->where("parentid='1' and name!='网站公告'")->order("`order`")->select(); ?>			
                                <?php if(is_array($category)): foreach($category as $key=>$help): ?><ul class="m-instruction-list">
					<li class="m-instruction-list-item">
						<h5><i class="ico ico-instruction ico-instruction-<?php echo ($number); ?>"></i><?php echo ($help['name']); ?></h5>
						<ul class="list">	
						
				<?php  $article=D("wenzhang")->where(array("cateid"=>$help['cateid']))->select(); foreach($article as $art){ echo "<li><a href='".C("URL_DOMAIN").'index/show/d/'.$art['id'].".html' target='_blank'>".$art['title'].'</a></li>'; } ?>
						</ul>
					</li>
					<input type='Hidden' value="<?php echo ($number++); ?>" />
				</ul><?php endforeach; endif; ?>
				 
			</div>
			<!--<div class="g-side1">-->
				<div class="g-side-l">
					<ul class="m-instruction-state f-clear">
						<li><i class="ico ico-state-l ico-state-l-2"></i><?php echo (L("web_user_guaranteed")); ?></li>
						<li><i class="ico ico-state-l ico-state-l-3"></i><?php echo (L("web_user_protection")); ?></li>
						<li><i class="ico ico-state-l ico-state-l-1"></i><?php echo (L("web_user_impartial")); ?></li>
					</ul>
				</div>
				<!--<div class="g-side-r">-->
					<!--<div class="m-instruction-yxCode">-->
						<!--<img width="100%" src="/Public/uploads/<?php echo C('web_logo1');?>" />-->
						<!--<p style="line-height: 12px;margin-top:2px;">微信公共号</p>-->
					<!--</div>-->
					<!--<div class="m-instruction-service">-->

						<!--<p>服务热线：<font color="#da3553"><?php echo C('cell');?></font></p>-->
						<?php
 if(C('qq_qun')){ $qq_qun_list = C('qq_qun'); $qq_qun_list = explode("|",$qq_qun_list); foreach($qq_qun_list as $qq){ $qq = trim($qq); ?>
						<!--<p><span class="ft-qqicon"><a style="text-indent:0em; background:none;width:160px;" target="_blank" rel="nofollow" href="javascript:;">官方QQ群：<em class="orange Fb"><?php echo ($qq); ?></em></a>  </span></p>-->
						<?php
 } } ?>
					<!--</div>-->
				<!--</div>-->
			<!--</div>-->
		</div>
	</div>
	<!--<div class="m-copyright">-->
		<!--<?php echo R('base/Getheader',array('foot'));?>&nbsp;|&nbsp;<?php echo C('web_copyright');?> &nbsp;&nbsp;-->
	<!--</div>-->
        <!--<div class="m-copyright">-->
		<!--<div class="g-wrap">-->
			<!--<ul class="m-QC-list">-->
				<!--<li><a style="width: 108px;"  target="_blank">&nbsp;</a></li>-->
				<!--<li><a style="width: 84px;" target="_blank">&nbsp;</a></li>-->
				<!--<li><a style="width: 104px;" target="_blank">&nbsp;</a></li>-->
				<!--<li><a style="width: 82px;" target="_blank">&nbsp;</a></li>-->
				<!---->
			<!--</ul>-->
		<!--</div>-->
		<!--</div>-->

</div>
<!--footer end-->
       <div style="display:none"><dd title=<?php echo (L("web_user_date_time")); ?> style="background:url(/Public/plugin/style/images/img/time.png) no-repeat 0px 6px;background-size:21px;"  class="specialFamily" id='sp_ServerTime'> </div>
			
 <script type="text/javascript" src="/Public/piyungou/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/Public/piyungou/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/Public/style/js/json2.js"></script> 
  <script type="text/javascript">
	$(function(){				
					var week = '日一二三四五六';
					var innerHtml = '{0}:{1}:{2}';
					var dateHtml = "{0}月{1}日 &nbsp;周{2}";
					var timer = 0;
					var beijingTimeZone = 8;				
							function format(str, json){
								return str.replace(/{(d)}/g, function(a, key) {
									return json[key];
								});
							}				
							function p(s) {
								return s < 10 ? '0' + s : s;
							}			

							function showTime(time){
								var timeOffset = ((-1 * (new Date()).getTimezoneOffset()) - (beijingTimeZone * 60)) * 60000;
								var now = new Date(time - timeOffset);
								document.getElementById('sp_ServerTime').innerHTML = format(innerHtml, [p(now.getHours()), p(now.getMinutes()), p(now.getSeconds())]);				
								
							}				
							
							window.yungou_time = 	function(time){						
								showTime(time);
								timer = setInterval(function(){
									time += 1000;
									showTime(time);
								}, 1000);					
							}
				window.yungou_time(<?php echo time()*1000; ?>);
							
			});
			$(document).ready(function(){
				try{  
			       if(typeof(eval(pleasereg_initx))=="function"){
			            pleasereg_initx();
				   }
			    }catch(e){
			       //alert("not function"); 
			    }  
			})
			// //云购基金
			// $.ajax({
			// 	url:"/api/fund/get",
			// 	success:function(msg){
			// 		$("#spanFundTotal").text(msg);
			// 	}
			// });
//计算购物袋数量
function cartCount(){
	var cart = jaaulde.utils.cookies.get("Cartlist");
	if(cart!=null&& cart!=''&&cart != "undefined"){
	$.get("/goods/che1/"+ new Date().getTime(),function(data){
		$("#cartCount").html("("+data+")");
		$("#row").html("("+data+")");	
	});
	
	
	}else{
		$("#cartCount").html("(0)");
	}
}
//加载购物袋信息
var row = 0;
var failure =  new Array();
function loadCart(){
	$.ajax({
		type:'get',
		url:"/goods/che/"+ new Date().getTime(),
		dataType:'json',
		success:function(headJosn){
			var str ='<dt><b><?php echo (L("web_user_gouwudai")); ?></b><a href="/goods/cartlist"><?php echo (L("web_user_fullscreen")); ?></a></dt><div id="cartListDiv" class="right-hide-scroll">';
			row = headJosn.cartList.length;
			if(headJosn.cartList.length > 0){
				for(i=0;i<headJosn.cartList.length;i++){
				
					$("#noCart").hide();
					str += '<dd id="dd_'+i+'">';    
					
					str += '<a href="javascript:gotoGoods('+headJosn.cartList[i].gid+','+headJosn.cartList[i].online.periodCurrent+')"><img width="60" height="60" src="/Public/uploads/'+headJosn.cartList[i].online.showImages.split+'"/></a>';
					
					
					var a =headJosn.cartList[i].online.priceTotal-headJosn.cartList[i].online.priceSell;
					
					if(headJosn.cartList[i].type!=3){
						str += '<div class="yfixed-divs-r"> <div class="yfixed-divs-rfsss">';     
						str += '<p><?php echo (L("web_user_cloud")); ?><em id="htime_'+i+'">'+headJosn.cartList[i].times+'</em></p>';
						var price = headJosn.cartList[i].times*(headJosn.cartList[i].buyPeriod);
						str += '<p><?php echo (L("web_user_subtotal")); ?><em id="hprice_'+i+'">￥'+price+'</em></p>';
						str += '</div><div class="yfixed-divs-rtsss"><p> </p>';    
						str += '</p><i onclick="delshop('+headJosn.cartList[i].gid+','+i+')"></i></div></div>';
					}else{
						str += '<div class="yfixed-divs-r">';     
						str +='<input  type="hidden" id="htimes_'+i+'" value="'+headJosn.cartList[i].online.priceAll+'"/>';     
						str += '<p><?php echo (L("web_user_cloud")); ?><em id="htimes_'+i+'">'+headJosn.cartList[i].online.priceAll+'</em></p>';
						str += '<p><?php echo (L("web_user_subtotal")); ?><em id="hprice_'+i+'">￥'+headJosn.cartList[i].online.priceAll+'</em></p><i onclick="delshop('+headJosn.cartList[i].gid+','+i+')"></i></div>';
					}
					str +='<input  type="hidden" id="harea_'+i+'" value="'+headJosn.cartList[i].online.priceArea+'"/>';     
					str +='<input  type="hidden" id="gid_'+i+'" value="'+headJosn.cartList[i].gid+'"/>'; 
					str +='<input  type="hidden" id="hbuyPeriod_'+i+'" value="'+headJosn.cartList[i].buyPeriod+'"/>'; 
					str +='<input  type="hidden" id="id_'+i+'" value="'+headJosn.cartList[i].id+'"/></dd>';     
				}
				str +='</div>';     
				$("#list").html(str);
				$("#cartListDiv").css({height:$(window).height()-136+"px"});
				hsetTotal(-1);
			}
			//鼠标移到每条记录上
			$(".yfixed-divs-r").hover(function(){
		        if($(".yfixed-divs-rf")){
			        $(this).find(".yfixed-divs-rt").show();
			        $(this).find(".yfixed-divs-rf").hide();
		        }
		    },function(){
		        if($(".yfixed-divs-rt")){
		            $(this).find(".yfixed-divs-rt").hide();
		            $(this).find(".yfixed-divs-rf").show();
		        }
		    })
		}
	})
}
var showCart = true;
//鼠标移到购物袋上
$(".Left-fixed-divs3").hover(function(){
	var cart = jaaulde.utils.cookies.get("Cartlist");
	if(cart!=null&& cart!=''&&cart != "undefined"){
	    $(".Left-fixed-divs2").show();
	    if(showCart){
	    	showCart = false;
	    	loadCart();
	    }
	}
},function(){
    $(".Left-fixed-divs2").hide();
})
//计算总价
function hsetTotal(a){
	if(a>=0){
		$("#row").html(row);
		var times = $("#htimes_"+a).val()==''?1:$("#htimes_"+a).val();
		var area = $("#harea_"+a).val()==''?1:$("#harea_"+a).val();
		var buyPeriod = $("#hbuyPeriod_"+a).val()==''?1:$("#hbuyPeriod_"+a).val();
		if(typeof(area) == "undefined")
			area=1;
		if(typeof(buyPeriod) == "undefined")
			buyPeriod=1;
		if(typeof(times) == "undefined")
			times=$("#buyAllTimes_"+a).html()==null?1:$("#buyAllTimes_"+a).html();
		var price=times*area*(parseInt(buyPeriod));
		$("#hprice_"+a).html('￥'+price);
	}
	var sum = 0;
	for(var i=0;i<row;i++){
		if(failure.toString().indexOf(i+',') <0){
		   sum+=parseInt($("#hprice_"+i).html().replace("￥", ""));
		}
	}
	$("#hpriceTotal").html(sum);
}
//加次数
function haddTimes(a){
	var times = $("#htimes_"+a).val();
	if(parseInt(times)<parseInt($("#hsurplus_"+a).val())){
		$.ajax({
			type:'get',
			url:"/goods/che",
			dataType:'json',
			data: {buyPeriod:$("#hbuyPeriod_"+a).val(),
				num:$("#htimes_"+a).val(),
				id:$("#gid_"+a).val()},
			success:function(result){
				if(result.status){
					var times = $("#htimes_"+a).val();
					$("#htimes_"+a).val(parseInt(times)+1);
					$("#htime_"+a).html(parseInt(times)+1);
					hsetTotal(a);
					var cart = jaaulde.utils.cookies.get("Cartlist");
					cart = eval( cart );
					cart[a].times=(times/1)+1;
					jaaulde.utils.cookies.set('Cartlist',JSON.stringify(cart),{path:"/"});
				}
				if(typeof($('.c_shop_bag')) != "undefined"){
					cartAjax();
				}
			}
		});
	}
}
//减次数
function hminusTimes(a){
	var times = $("#htimes_"+a).val();
	if(times>1){
		$("#htimes_"+a).val(parseInt(times)-1);
		$("#htime_"+a).html(parseInt(times)-1);
		setTotal(a);
		var cart = jaaulde.utils.cookies.get("Cartlist");
		var list = eval( cart );
		list[a].times=(times/1)-1;
		jaaulde.utils.cookies.set('Cartlist',JSON.stringify(list),{path:"/"});
		if(typeof($('.c_shop_bag')) != "undefined"){
			cartAjax();
		}
	}
}
//删除

function delshop(id,a){
	var Cartlist = $.cookie('Cartlist');	
	var info = $.evalJSON(Cartlist);
	var num=parseInt($("#rCartTotal2").html())-1;
	var sum=parseInt($("#rCartTotalM").html());
	info['MoenyCount'] = sum-info[id]['money']*info[id]['num'];
		
	delete info[id];
	
	$.cookie('Cartlist',$.toJSON(info),{expires:30,path:'/'});
	
	$("#dd_"+a).remove();
	if($('#list').find('dd').length<1){
		$(".Left-fixed-divs2").show();
		 $(".Left-fixed-divs3").hover(function(){
			         $(".Left-fixed-divs2").show();
				 $.get("/api/gou/che1/"+ new Date().getTime(),function(data){
		if(data<1){
		$("#noCart").show();
		}
	});
			    },function(){
			         $(".Left-fixed-divs2").hide();
			    })
	}
	failure.push(a+',');
	row=row-1;
	hsetTotal(-1);
	cartCount();
	if($('.c_shop_bag div').length>0){
		cartAjax();
	}
}
function hdel(a){
	var cart = jaaulde.utils.cookies.get("Cartlist");
	var cartList = eval( cart );
	if(cartList.length==1){
		jaaulde.utils.cookies.set('Cartlist','',{path:"/"});
	}else{
		cartList.splice(a,1)
		jaaulde.utils.cookies.set('Cartlist',JSON.stringify(cartList),{path:"/"});
	}
	$("#dd_"+a).remove();
	if($('#list').find('dd').length<1){
		$(".Left-fixed-divs2").hide();
		 $(".Left-fixed-divs3").hover(function(){
			         $(".Left-fixed-divs2").hide();
			    },function(){
			         $(".Left-fixed-divs2").hide();
			    })
	}
	failure.push(a+',');
	row=row-1;
	hsetTotal(-1);
	cartCount();
	if($('.c_shop_bag div').length>0){
		cartAjax();
	}
}
</script> 
  <script type="text/javascript" src="/Public/style/js/index.js"></script> 
		
  <script type="text/javascript" src="/Public/style/js/indexExt.js"></script> 
   <script>
					var attr=0;
					 attr=$(".yJoinNum input").val();
					var attr1=[];
					var nums=0;
					for(i=0;i<attr.length;i++){
						var nums=attr.slice(i,i+1);
						attr1.push(nums);
					}
					$(".yNumList ul").css("marginTop","-270px");
				 	var list=0;
				 	if($(".yNumList").length<attr1.length){
							var more=attr1.length-$(".yNumList").length;
							for(i=0;i<more;i++){
								$($(".yNumList")[0]).clone(true).insertAfter($($(".yNumList")[$(".yNumList").length-1]))
							}
						}
				 	for(i=0;i<attr1.length;i++){
				 		list=attr[i];
						$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},2000)
					}
					//setInterval(function(){
						
						attr1=[];
						attr=$(".yJoinNum input").val();
						for(i=0;i<attr.length;i++){
							var nums=attr.slice(i,i+1);
							attr1.push(nums);
						}
						if($(".yNumList").length<attr1.length){
							var more=attr1.length-$(".yNumList").length;
							for(i=0;i<more;i++){
								$($(".yNumList")[0]).clone(true).insertAfter($($(".yNumList")[$(".yNumList").length-1]))
							}
						}
						$(".yNumList ul").css("marginTop","-270px");
						for(i=0;i<attr1.length;i++){
					 		list=attr[i];
							$($(".yNumList ul")[i]).animate({marginTop:(list*30-270)},2000)
						}
					//},10000)

					$(function(){
						for(i=0;i<$("#user").length;i++){       
					      	if($($("#user")[i]).html().length>10){
					      	$($("#user")[i]).html($($("#user")[i]).html().slice(0,10)+"...")
					     	}
					    }
					 })
			</script>
  <script type="text/javascript">
$(function(){
	
});



</script>  



<div style="display:none"><script language="javascript" type="text/javascript" src="http://js.users.51.la/18658291.js"></script>
<noscript><a href="http://www.51.la/?18658291" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/18658291.asp" style="border:none" /></a></noscript></div>
 </body>
</html>