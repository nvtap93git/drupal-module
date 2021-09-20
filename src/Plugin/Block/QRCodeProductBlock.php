<?php

namespace Drupal\product\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Endroid\QrCode\QrCode;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCodeBundle\Response;
use Endroid\QrCode\QrCodeInterface;
use Drupal\Core\Url;


/**
 * Provides a 'QR Code Product Block' Block
 *
 * @Block(
 *   id = "qr_code_product_block",
 *   admin_label = @Translation("QR Code Product Block"),
 * )
 */
class QRCodeProductBlock extends BlockBase{
  /**
  * {@inheritdoc}
  */
  public function build() {
    $params = \Drupal::routeMatch()->getParameter('product');
    $parameter_id = \Drupal::routeMatch()->getRawParameter('product');

    if (empty($params) || empty($parameter_id)){
      return;
    }

    $uri = Url::fromRoute('entity.product.qr.generator', [
      'productID' => $parameter_id,
    ])->toString();

    return array(
      '#type' => 'markup',
      '#markup' => '<img data-drupal-selector="edit-product-image-0-preview" src="'.$uri.'" width="100" height="81" alt="" typeof="foaf:Image" class="image-style-thumbnail">',
    );
  }
}
