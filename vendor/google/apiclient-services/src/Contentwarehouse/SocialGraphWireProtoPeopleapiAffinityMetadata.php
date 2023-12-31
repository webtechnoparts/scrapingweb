<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Contentwarehouse;

class SocialGraphWireProtoPeopleapiAffinityMetadata extends \Google\Model
{
  protected $clientInteractionInfoType = SocialGraphWireProtoPeopleapiAffinityMetadataClientInteractionInfo::class;
  protected $clientInteractionInfoDataType = '';
  protected $cloudDeviceDataInfoType = SocialGraphWireProtoPeopleapiAffinityMetadataCloudDeviceDataInfo::class;
  protected $cloudDeviceDataInfoDataType = '';
  public $cloudScore;
  /**
   * @var string
   */
  public $suggestionConfidence;

  /**
   * @param SocialGraphWireProtoPeopleapiAffinityMetadataClientInteractionInfo
   */
  public function setClientInteractionInfo(SocialGraphWireProtoPeopleapiAffinityMetadataClientInteractionInfo $clientInteractionInfo)
  {
    $this->clientInteractionInfo = $clientInteractionInfo;
  }
  /**
   * @return SocialGraphWireProtoPeopleapiAffinityMetadataClientInteractionInfo
   */
  public function getClientInteractionInfo()
  {
    return $this->clientInteractionInfo;
  }
  /**
   * @param SocialGraphWireProtoPeopleapiAffinityMetadataCloudDeviceDataInfo
   */
  public function setCloudDeviceDataInfo(SocialGraphWireProtoPeopleapiAffinityMetadataCloudDeviceDataInfo $cloudDeviceDataInfo)
  {
    $this->cloudDeviceDataInfo = $cloudDeviceDataInfo;
  }
  /**
   * @return SocialGraphWireProtoPeopleapiAffinityMetadataCloudDeviceDataInfo
   */
  public function getCloudDeviceDataInfo()
  {
    return $this->cloudDeviceDataInfo;
  }
  public function setCloudScore($cloudScore)
  {
    $this->cloudScore = $cloudScore;
  }
  public function getCloudScore()
  {
    return $this->cloudScore;
  }
  /**
   * @param string
   */
  public function setSuggestionConfidence($suggestionConfidence)
  {
    $this->suggestionConfidence = $suggestionConfidence;
  }
  /**
   * @return string
   */
  public function getSuggestionConfidence()
  {
    return $this->suggestionConfidence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SocialGraphWireProtoPeopleapiAffinityMetadata::class, 'Google_Service_Contentwarehouse_SocialGraphWireProtoPeopleapiAffinityMetadata');
