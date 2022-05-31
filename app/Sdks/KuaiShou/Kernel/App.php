<?php
namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Container\AccountReportApiContainer;
use App\Sdks\KuaiShou\Container\AdUnitApiContainer;
use App\Sdks\KuaiShou\Container\AdvertiserApiContainer;
use App\Sdks\KuaiShou\Container\AsyncTackApiContainer;
use App\Sdks\KuaiShou\Container\CampaignApiContainer;
use App\Sdks\KuaiShou\Container\CreativeApiContainer;
use App\Sdks\KuaiShou\Container\CreativeReportApiContainer;
use App\Sdks\KuaiShou\Container\ImageApiContainer;
use App\Sdks\KuaiShou\Container\OauthApiContainer;
use App\Sdks\KuaiShou\Container\ProgramCreativeApiContainer;
use App\Sdks\KuaiShou\Container\ProgramCreativeReportApiContainer;
use App\Sdks\KuaiShou\Container\TrackApiContainer;
use App\Sdks\KuaiShou\Container\VideoApiContainer;
use GuzzleHttp\Client;


class App
{
    /** @var Client 实例 */
    public $client;

    /** @var OauthApiContainer */
    public $oauthApiContainer;

    /** @var TrackApiContainer */
    public $trackApiContainer;

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

    /*** @var VideoApiContainer */
    public $videoApiContainer;

    /*** @var ImageApiContainer */
    public $imageApiContainer;



    public function oauth(): OauthApiContainer
    {
        if (empty($this->oauthApiContainer)) {
            $container = new OauthApiContainer();
            $container->init($this, $this->getClient());
            $this->oauthApiContainer = $container;
        }
        return $this->oauthApiContainer;
    }


    public function track(): TrackApiContainer
    {
        if (empty($this->trackApiContainer)) {
            $container = new TrackApiContainer();
            $container->init($this, $this->getClient());
            $this->trackApiContainer = $container;
        }
        return $this->trackApiContainer;
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


    public function programCreativeReport(): ProgramCreativeReportApiContainer
    {
        if (empty($this->programCreativeReportApiContainer)) {
            $container = new ProgramCreativeReportApiContainer();
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

    public function video(): VideoApiContainer
    {
        if (empty($this->videoApiContainer)) {
            $container = new VideoApiContainer();
            $container->init($this, $this->getClient());
            $this->videoApiContainer = $container;
        }
        return $this->videoApiContainer;
    }

    public function image(): ImageApiContainer
    {
        if (empty($this->imageApiContainer)) {
            $container = new ImageApiContainer();
            $container->init($this, $this->getClient());
            $this->imageApiContainer = $container;
        }
        return $this->imageApiContainer;
    }




}
