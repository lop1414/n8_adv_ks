<?php
namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Container\AdUnitApiContainer;
use App\Sdks\KuaiShou\Container\AdvertiserApiContainer;
use App\Sdks\KuaiShou\Container\CampaignApiContainer;
use App\Sdks\KuaiShou\Container\MultipleCampaignApiContainer;
use GuzzleHttp\Client;


class App
{
    /** @var Client 实例 */
    public $client;


    /** @var AdvertiserApiContainer */
    public $advertiserApiContainer;

    /** @var CampaignApiContainer */
    public $campaignApiContainer;

    /** @var MultipleCampaignApiContainer */
    public $multipleCampaignApiContainer;

    /** @var AdUnitApiContainer */
    public $adUnitApiContainer;



    /**
     * @return AdvertiserApiContainer
     */
    public function advertiser(): AdvertiserApiContainer
    {
        if (empty($this->advertiserApiContainer)) {
            $container = new AdvertiserApiContainer();
            $container->init($this, $this->getClient());
            $this->advertiserApiContainer = $container;
        }
        return $this->advertiserApiContainer;
    }

    /**
     * @return CampaignApiContainer
     */
    public function campaign(): CampaignApiContainer
    {
        if (empty($this->campaignApiContainer)) {
            $container = new CampaignApiContainer();
            $container->init($this, $this->getClient());
            $this->campaignApiContainer = $container;
        }
        return $this->campaignApiContainer;
    }

    /**
     * @return MultipleCampaignApiContainer
     */
    public function multipleCampaign(): MultipleCampaignApiContainer
    {
        if (empty($this->multipleCampaignApiContainer)) {
            $container = new MultipleCampaignApiContainer();
            $container->init($this, $this->getClient());
            $this->multipleCampaignApiContainer = $container;
        }
        return $this->multipleCampaignApiContainer;
    }

    /**
     * @return AdUnitApiContainer
     */
    public function adUnit(): AdUnitApiContainer
    {
        if (empty($this->adUnitApiContainer)) {
            $container = new AdUnitApiContainer();
            $container->init($this, $this->getClient());
            $this->adUnitApiContainer = $container;
        }
        return $this->adUnitApiContainer;
    }


}
