import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../screens/category_listings_screen.dart';

/// OLX-style visual category model with 3D luxury image asset support
class CategoryButtonItem {
  final int id;
  final String label;
  final IconData icon;
  final Color tint;
  final String subtitle;
  final String imageAsset;

  const CategoryButtonItem({
    required this.id,
    required this.label,
    required this.icon,
    required this.tint,
    required this.subtitle,
    required this.imageAsset,
  });
}

/// Visual 2-row x 5-column category grid matching classic OLX interface
class OlxCategoryGrid extends ConsumerWidget {
  const OlxCategoryGrid({super.key});

  static const List<CategoryButtonItem> row1 = [
    CategoryButtonItem(
      id: 3,
      label: 'Mobiles',
      icon: Icons.phone_iphone_rounded,
      tint: Color(0xFF60A5FA), // Sky Cyan
      subtitle: 'Phones & Tabs',
      imageAsset: 'assets/icons/cat_mobiles.png',
    ),
    CategoryButtonItem(
      id: 1,
      label: 'Vehicles',
      icon: Icons.directions_car_filled_rounded,
      tint: Color(0xFF94A3B8), // Silver Car
      subtitle: 'Cars & Bikes',
      imageAsset: 'assets/icons/cat_vehicles.png',
    ),
    CategoryButtonItem(
      id: 5,
      label: 'Property\nfor Sale',
      icon: Icons.holiday_village_rounded,
      tint: Color(0xFFF87171), // Coral Red House
      subtitle: 'Plots & Homes',
      imageAsset: 'assets/icons/cat_property_sale.png',
    ),
    CategoryButtonItem(
      id: 5,
      label: 'Property\nfor Rent',
      icon: Icons.vpn_key_rounded,
      tint: Color(0xFF38BDF8), // Blue Key
      subtitle: 'Rentals',
      imageAsset: 'assets/icons/cat_property_rent.png',
    ),
    CategoryButtonItem(
      id: 6,
      label: 'Electronics\n& Home...',
      icon: Icons.camera_alt_rounded,
      tint: Color(0xFFA78BFA), // Lavender Camera
      subtitle: 'Gadgets & TV',
      imageAsset: 'assets/icons/cat_electronics.png',
    ),
  ];

  static const List<CategoryButtonItem> row2 = [
    CategoryButtonItem(
      id: 7,
      label: 'Services',
      icon: Icons.format_paint_rounded,
      tint: Color(0xFFFB923C), // Orange Roller
      subtitle: 'Home & Expert',
      imageAsset: 'assets/icons/cat_services.png',
    ),
    CategoryButtonItem(
      id: 8,
      label: 'Jobs',
      icon: Icons.business_center_rounded,
      tint: Color(0xFFFBBF24), // Leather Briefcase
      subtitle: 'Careers & Hiring',
      imageAsset: 'assets/icons/cat_jobs.png',
    ),
    CategoryButtonItem(
      id: 9,
      label: 'Animals',
      icon: Icons.pets_rounded,
      tint: Color(0xFFFDE047), // Yellow Pets
      subtitle: 'Pets & Birds',
      imageAsset: 'assets/icons/cat_animals.png',
    ),
    CategoryButtonItem(
      id: 10,
      label: 'Furniture &\nHome De...',
      icon: Icons.chair_rounded,
      tint: Color(0xFF818CF8), // Indigo Armchair
      subtitle: 'Living & Decor',
      imageAsset: 'assets/icons/cat_furniture.png',
    ),
    CategoryButtonItem(
      id: 11,
      label: 'Fashion &\nBeauty',
      icon: Icons.watch_rounded,
      tint: Color(0xFFF43F5E), // Rose Watch
      subtitle: 'Wear & Style',
      imageAsset: 'assets/icons/cat_fashion.png',
    ),
  ];

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Row 1 (5 items)
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: row1.map((cat) => Expanded(
              child: _CategoryTile(
                category: cat,
                onTap: () => _navigateToCategory(context, cat),
              ),
            )).toList(),
          ),

          const SizedBox(height: 10),

          // Row 2 (5 items)
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: row2.map((cat) => Expanded(
              child: _CategoryTile(
                category: cat,
                onTap: () => _navigateToCategory(context, cat),
              ),
            )).toList(),
          ),
        ],
      ),
    );
  }

  void _navigateToCategory(BuildContext context, CategoryButtonItem cat) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => CategoryListingsScreen(
          categoryId: cat.id,
          categoryName: cat.label.replaceAll('\n', ' '),
          icon: cat.icon,
          accentColor: cat.tint,
        ),
      ),
    );
  }
}

class _CategoryTile extends StatefulWidget {
  final CategoryButtonItem category;
  final VoidCallback onTap;

  const _CategoryTile({
    required this.category,
    required this.onTap,
  });

  @override
  State<_CategoryTile> createState() => _CategoryTileState();
}

class _CategoryTileState extends State<_CategoryTile> {
  double _scale = 1.0;

  @override
  Widget build(BuildContext context) {
    final cat = widget.category;

    return GestureDetector(
      onTapDown: (_) => setState(() => _scale = 0.90),
      onTapUp: (_) {
        setState(() => _scale = 1.0);
        widget.onTap();
      },
      onTapCancel: () => setState(() => _scale = 1.0),
      behavior: HitTestBehavior.opaque,
      child: AnimatedScale(
        scale: _scale,
        duration: const Duration(milliseconds: 110),
        curve: Curves.easeInOut,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 2),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // OLX-Style dark square tile with subtle rounded corners
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  color: const Color(0xFF191E2B),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: const Color(0xFF2B3245),
                    width: 1.0,
                  ),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x33000000),
                      blurRadius: 6,
                      offset: Offset(0, 2),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(13),
                  child: Image.asset(
                    cat.imageAsset,
                    width: 56,
                    height: 56,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _buildVectorIcon(cat),
                  ),
                ),
              ),

              const SizedBox(height: 5),

              // Two-line clean typography label
              SizedBox(
                height: 28,
                child: Text(
                  cat.label,
                  style: const TextStyle(
                    fontSize: 9.5,
                    height: 1.15,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFFCBD5E1),
                    letterSpacing: -0.1,
                  ),
                  textAlign: TextAlign.center,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildVectorIcon(CategoryButtonItem cat) {
    return Center(
      child: Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: cat.tint.withValues(alpha: 0.16),
        ),
        child: Icon(
          cat.icon,
          color: cat.tint,
          size: 22,
        ),
      ),
    );
  }
}
