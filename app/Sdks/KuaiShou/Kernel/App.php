<?php
namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Container\AdUnitApiContainer;
use App\Sdks\KuaiShou\Container\AdvertiserApiContainer;
use App\Sdks\KuaiShou\Container\CampaignApiContainer;
use App\Sdks\KuaiShou\Container\CreativeApiContainer;
use App\Sdks\KuaiShou\Container\OauthApiContainer;
use App\Sdks\KuaiShou\Container\ProgramCreativeApiContainer;
use GuzzleHttp\Client;


class App
{
    /** @var Client 实例 */
    public $client;

    /** @var OauthApiContainer */
    public $oauthApiContainer;

    /** @var AdvertiserApiContainer */
    public $advertiserApiContainer;

    /** @var CampaignApiContainer */
    public $campaignApiContainer;

    /** @var AdUnitApiContainer */
    public $adUnitApiContainer;

    /** @var CreativeApiContainer */
    public $creativeApiContainer;

    /** @var ProgramCreativeApiContainer */
    public $programCreativeApiContainer;



    /**
     * @return OauthApiContainer
     */
    public function oauth(): OauthApiContainer
    {
        if (empty($this->oauthApiContainer)) {
            $container = new OauthApiContainer();
            $container->init($this, $this->getClient());
            $this->oauthApiContainer = $container;
        }
        return $this->oauthApiContainer;
    }

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

    /**
     * @return CreativeApiContainer
     */
    public function creative(): CreativeApiContainer
    {
        if (empty($this->creativeApiContainer)) {
            $container = new CreativeApiContainer();
            $container->init($this, $this->getClient());
            $this->creativeApiContainer = $container;
        }
        return $this->creativeApiContainer;
    }

    /**
     * @return ProgramCreativeApiContainer
     */
    public function programCreative(): ProgramCreativeApiContainer
    {
        if (empty($this->programCreativeApiContainer)) {
            $container = new ProgramCreativeApiContainer();
            $container->init($this, $this->getClient());
            $this->programCreativeApiContainer = $container;
        }
        return $this->programCreativeApiContainer;
    }




}
