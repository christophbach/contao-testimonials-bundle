<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Controller\ContentElement;

use Contao\Template;
use Contao\ContentModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Plenta\ContaoTestimonialsBundle\Helper\Testimonial;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

class TestimonialContentElementController extends AbstractContentElementController
{
    public const TYPE = 'plenta_testimonial';

    private Testimonial $testimonial;

    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if ('single' === (string) $model->testimonial_source) {
            $testimonial[0] = $this->testimonial->getTestimonialById((int) $model->testimonialId);
        } else {
            $testimonial = $this->testimonial->getTestimonialsByArchive(
                (int) $model->testimonial_archive,
                1,
                true,
                $model->plenta_testimonials_categories
            );
        }

        if (null !== $testimonial) {
            $template->name = $testimonial[0]['name'];
            $template->company = $testimonial[0]['company'];
            $template->department = $testimonial[0]['department'];
            $template->testimonial_heading = $testimonial[0]['testimonial_heading'];
            $template->testimonial = $testimonial[0]['testimonial'];
            $template->rating = $this->testimonial->getRating((int) $testimonial[0]['rating']);
        }

        $template->addImage = false;

        if (true === (bool) $model->testimonial_addImages && $testimonial[0]['addImage'] && $testimonial[0]['singleSRC']) {
            $this->testimonial->addImageToTemplate($template, $model, $testimonial[0]['singleSRC']);
        }

        $template->addRating = $model->testimonial_addRatings;

        return $template->getResponse();
    }
}
