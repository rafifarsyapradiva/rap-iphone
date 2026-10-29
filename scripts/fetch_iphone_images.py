#!/usr/bin/env python3
"""
fetch_iphone_images.py
Downloads high-resolution official Apple product renders from Apple Store CDN (fmt=png-alpha),
processes them into centered 800x800 WebP images with transparent backgrounds,
and saves them into public/images/products/.
"""

import os
import io
import urllib.request
from PIL import Image

TARGET_DIR = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'public', 'images', 'products')

# Mapping of file names to Apple CDN URLs
ASSETS = {
    # 1. iPhone 16 Pro Max
    'iphone-16-pro-max.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-max-desert-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-max-natural-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-max-white-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-max-black-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha',

    # 2. iPhone 16 Pro
    'iphone-16-pro.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-3inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-natural-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-3inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-desert-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-3inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-white-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-3inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pro-black-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-3inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha',

    # 3. iPhone 16
    'iphone-16.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-ultramarine?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-ultramarine.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-ultramarine?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-teal.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-teal?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-pink.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-pink?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-white.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-white?wid=940&hei=1112&fmt=png-alpha',
    'iphone-16-black.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-black?wid=940&hei=1112&fmt=png-alpha',

    # 4. iPhone 15 Pro
    'iphone-15-pro.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-1inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-15-pro-natural-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-1inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha',
    'iphone-15-pro-blue-titanium.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-1inch-bluetitanium?wid=940&hei=1112&fmt=png-alpha',

    # 5. iPhone 14
    'iphone-14.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-14-finish-select-202209-6-1inch-midnight?wid=940&hei=1112&fmt=png-alpha',
    'iphone-14-midnight.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-14-finish-select-202209-6-1inch-midnight?wid=940&hei=1112&fmt=png-alpha',
    'iphone-14-starlight.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-14-finish-select-202209-6-1inch-starlight?wid=940&hei=1112&fmt=png-alpha',
    'iphone-14-blue.webp': 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-14-finish-select-202209-6-1inch-blue?wid=940&hei=1112&fmt=png-alpha',

    # 6. iPhone 13
    'iphone-13.webp': 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-finish-select-202207-6-1inch-midnight?wid=940&hei=1112&fmt=png-alpha',
    'iphone-13-midnight.webp': 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-finish-select-202207-6-1inch-midnight?wid=940&hei=1112&fmt=png-alpha',
    'iphone-13-pink.webp': 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-finish-select-202207-6-1inch-pink?wid=940&hei=1112&fmt=png-alpha',
}

CANVAS_SIZE = (800, 800)

def process_and_save(image_bytes: bytes, output_path: str):
    """
    Crops the active non-empty alpha content, fits it nicely inside CANVAS_SIZE with padding,
    and exports as WebP with transparency.
    """
    im = Image.open(io.BytesIO(image_bytes)).convert("RGBA")
    
    # Get active bounding box
    bbox = im.getbbox()
    if bbox:
        im = im.crop(bbox)
        
    # Scale while maintaining aspect ratio to fit inside padded canvas (720x720 inside 800x800)
    max_w, max_h = 720, 720
    im.thumbnail((max_w, max_h), Image.Resampling.LANCZOS)
    
    # Create transparent canvas and paste in center
    canvas = Image.new("RGBA", CANVAS_SIZE, (0, 0, 0, 0))
    offset_x = (CANVAS_SIZE[0] - im.width) // 2
    offset_y = (CANVAS_SIZE[1] - im.height) // 2
    canvas.paste(im, (offset_x, offset_y), im)
    
    # Save as high-quality WebP
    canvas.save(output_path, "WEBP", quality=92, method=6)

def main():
    os.makedirs(TARGET_DIR, exist_ok=True)
    print(f"Target directory: {TARGET_DIR}")
    print(f"Downloading {len(ASSETS)} assets from Apple Store CDN...")
    
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
    }
    
    # Cache downloaded URLs so identical URLs aren't re-downloaded
    url_cache = {}
    
    for filename, url in ASSETS.items():
        out_path = os.path.join(TARGET_DIR, filename)
        try:
            if url in url_cache:
                raw_bytes = url_cache[url]
            else:
                req = urllib.request.Request(url, headers=headers)
                with urllib.request.urlopen(req, timeout=20) as resp:
                    raw_bytes = resp.read()
                url_cache[url] = raw_bytes
                
            process_and_save(raw_bytes, out_path)
            size_kb = os.path.getsize(out_path) / 1024
            print(f"[OK] {filename} ({size_kb:.1f} KB)")
        except Exception as e:
            print(f"[ERROR] Failed {filename}: {e}")

if __name__ == "__main__":
    main()

