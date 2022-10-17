<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>

<? if ($arResult['ITEMS']['CNT']):?>
<h2><?=Loc::getMessage('MAPLIST_TITLE');?></h2>

<div id="map" style="width:100%; height: 400px;"></div>

<script>
	let isMapSupport = true;

    try {
    ymaps.ready(function () {
        const myMap = new ymaps.Map(`map`, {
        center: [55.76, 37.64],
        zoom: 10
        }, {
        searchControlProvider: `yandex#search`
        });

        myClusterer = new ymaps.Clusterer({
            preset: 'islands#invertedGrayClusterIcons',
            clusterNumbers:[100],
            zoomMargin: [30],
            minClusterSize: [4],
        });

        var Placemark = {};

        var Items = <?php echo $arResult['ITEMS']['JSON'];?>

        $.each(Items, function(i, item) {
            Placemark[i] = new ymaps.Placemark([item.COODRINATES],{
                balloonContentBody: `<a href="${item.DETAIL_URL}">${item.NAME}</a>`,
                hintContent: item.NAME,
                balloonOffset: [5,0],
                balloonCloseButton: true,
                balloonMinWidth: 450,
                balloonMaxWidth: 450,
                balloonMinHeught: 150,
                balloonMaxHeught: 200,
                },
                {
                iconLayout: 'default#image',
                iconImageHref: '<?=SITE_TEMPLATE_PATH?>/img/pin@2x.png',
                iconImageSize: [21, 30],
            });
            myClusterer.add(Placemark[i]);
        });
            myMap.geoObjects.add(myClusterer);
            myClusterer.options.set('clusterIconColor', '#C76F8D')
            myMap.setBounds(myMap.geoObjects.getBounds(), {
            checkZoomRange: true,
            zoomMargin: 45
            });
    });

    } catch (err) {
    isMapSupport = false;
    }
</script>

<? 
endif;