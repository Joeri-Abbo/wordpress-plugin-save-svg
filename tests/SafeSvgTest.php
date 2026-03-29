<?php

use PHPUnit\Framework\TestCase;

/**
 * Expose protected methods for testing.
 */
class TestableSafeSvg extends safeSvg {
    public function publicIsGzipped( $contents ): bool {
        return $this->isGzipped( $contents );
    }
    public function publicSvgDimensions( $svg ): array|false {
        return $this->svgDimensions( $svg );
    }
}

class SafeSvgTest extends TestCase {

    private TestableSafeSvg $svg;

    protected function setUp(): void {
        $this->svg = new TestableSafeSvg();
    }

    // --- fixMimeTypeSvg ---

    public function testFixMimeTypeSvgSetsSvgType(): void {
        $data = [ 'ext' => 'svg', 'type' => '' ];
        $result = $this->svg->fixMimeTypeSvg( $data, null, 'image.svg', null );
        $this->assertSame( 'image/svg+xml', $result['type'] );
        $this->assertSame( 'svg', $result['ext'] );
    }

    public function testFixMimeTypeSvgSetsSvgzType(): void {
        $data = [ 'ext' => 'svgz', 'type' => '' ];
        $result = $this->svg->fixMimeTypeSvg( $data, null, 'image.svgz', null );
        $this->assertSame( 'image/svg+xml', $result['type'] );
        $this->assertSame( 'svgz', $result['ext'] );
    }

    public function testFixMimeTypeSvgDetectsExtFromFilename(): void {
        $data = [ 'ext' => '', 'type' => '' ];
        $result = $this->svg->fixMimeTypeSvg( $data, null, 'image.svg', null );
        $this->assertSame( 'image/svg+xml', $result['type'] );
    }

    public function testFixMimeTypeSvgIgnoresNonSvg(): void {
        $data = [ 'ext' => 'png', 'type' => 'image/png' ];
        $result = $this->svg->fixMimeTypeSvg( $data, null, 'image.png', null );
        $this->assertSame( 'image/png', $result['type'] );
    }

    // --- isGzipped ---

    public function testIsGzippedReturnsTrueForGzippedContent(): void {
        $content = gzencode( '<svg></svg>' );
        $this->assertTrue( $this->svg->publicIsGzipped( $content ) );
    }

    public function testIsGzippedReturnsFalseForPlainContent(): void {
        $this->assertFalse( $this->svg->publicIsGzipped( '<svg></svg>' ) );
    }

    // --- svgDimensions ---

    public function testSvgDimensionsReturnsCorrectWidthHeight(): void {
        $svgFile = dirname( __DIR__ ) . '/lib/vendor/enshrined/svg-sanitize/tests/data/svgTestOne.svg';
        if ( ! file_exists( $svgFile ) ) {
            $this->markTestSkipped( 'Test SVG fixture not found.' );
        }
        $result = $this->svg->publicSvgDimensions( $svgFile );
        $this->assertIsArray( $result );
        $this->assertArrayHasKey( 'width', $result );
        $this->assertArrayHasKey( 'height', $result );
        $this->assertArrayHasKey( 'orientation', $result );
    }

    public function testSvgDimensionsReturnsZeroDimensionsForNonExistentFile(): void {
        // simplexml_load_file returns false for missing files; svgDimensions returns 0-dimension array
        $result = $this->svg->publicSvgDimensions( '/nonexistent/file.svg' );
        $this->assertIsArray( $result );
        $this->assertEquals( 0, $result['width'] );
        $this->assertEquals( 0, $result['height'] );
    }

    // --- checkForSvg ---

    public function testCheckForSvgPassesThroughNonSvgFile(): void {
        $file = [ 'type' => 'image/png', 'tmp_name' => '' ];
        $result = $this->svg->checkForSvg( $file );
        $this->assertSame( $file, $result );
    }
}
