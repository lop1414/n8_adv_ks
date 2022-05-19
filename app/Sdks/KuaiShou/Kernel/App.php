<?php
namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Container\AccountReportApiContainer;
use App\Sdks\KuaiShou\Container\AdUnitApiContainer;
use App\Sdks\KuaiShou\Container\AdvertiserApiContainer;
use App\Sdks\KuaiShou\Container\AsyncTackApiContainer;
use App\Sdks\KuaiShou\Container\CampaignApiContainer;
use App\Sdks\KuaiShou\Container\CreativeApiContainer;
use App\Sdks\KuaiShou\Container\CreativeReportApiContainer;
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

    /** @var AccountReportApiContainer */
    public $accountReportApiContainer;

    /** @var CreativeReportApiContainer */
    public $creativeReportApiContainer;

    /*** @var AsyncTackApiContainer */
    public $asyncTackApiContainer;



    public function oauth(): OauthApiContainer
    {
        if (empty($this->oauthApiContainer)) {
            $container = new OauthApiContainer();
            $container->init($this, $this->getClient());
            $this->oauthApiContainer = $container;
        }
        return $this->oauthApiContainer;
    }



    public function advertiser(): AdvertiserApiContainer
    {
        if (empty($this->advertiserApiContainer)) {
            $container = new AdvertiserApiContainer();
            $container->init($this, $this->getClient());
            $this->advertiserApiContainer = $container;
        }
        return $this->advertiserApiContainer;
    }



    public function campaign(): CampaignApiContainer
    {
        if (empty($this->campaignApiContainer)) {
            $container = new CampaignApiContainer();
            $container->init($this, $this->getClient());
            $this->campaignApiContainer = $container;
        }
        return $this->campaignApiContainer;
    }



    public function adUnit(): AdUnitApiContainer
    {
        if (empty($this->adUnitApiContainer)) {
            $container = new AdUnitApiContainer();
            $container->init($this, $this->getClient());
            $this->adUnitApiContainer = $container;
        }
        return $this->adUnitApiContainer;
    }



    public function creative(): CreativeApiContainer
    {
        if (empty($this->creativeApiContainer)) {
            $container = new CreativeApiContainer();
            $container->init($this, $this->getClient());
            $this->creativeApiContainer = $container;
        }
        return $this->creativeApiContainer;
    }



    public function programCreative(): ProgramCreativeApiContainer
    {
        if (empty($this->programCreativeApiContainer)) {
            $container = new ProgramCreativeApiContainer();
            $container->init($this, $this->getClient());
            $this->programCreativeApiContainer = $container;
        }
        return $this->programCreativeApiContainer;
    }



    public function accountReport(): AccountReportApiContainer
    {
        if (empty($this->accountReportApiContainer)) {
            $container = new AccountReportApiContainer();
            $container->init($this, $this->getClient());
            $this->accountReportApiContainer = $container;
        }
        return $this->accountReportApiContainer;
    }



    public function creativeReport(): CreativeReportApiContainer
    {
        if (empty($this->creativeReportApiContainer)) {
            $container = new CreativeReportApiContainer();
            $container->init($this, $this->getClient());
            $this->creativeReportApiContainer = $container;
        }
        return $this->creativeReportApiContainer;
    }


    public function programCreativeReport(): ProgramCreativeApiContainer
    {
        if (empty($this->programCreativeReportApiContainer)) {
            $container = new ProgramCreativeApiContainer();
            $container->init($this, $this->getClient());
            $this->programCreativeReportApiContainer = $container;
        }
        return $this->programCreativeReportApiContainer;
    }


    public function asyncTack(): AsyncTackApiContainer
    {
        if (empty($this->asyncTackApiContainer)) {
            $container = new AsyncTackApiContainer();
            $container->init($this, $this->getClient());
            $this->asyncTackApiContainer = $container;
        }
        return $this->asyncTackApiContainer;
    }




}
