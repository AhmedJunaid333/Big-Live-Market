import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';

class CategoriesScreen extends ConsumerWidget {
  const CategoriesScreen({super.key});

  static const List<Map<String, dynamic>> _catalogCategories = [
    {'name': 'Motors & Cars', 'icon': Icons.directions_car_filled_rounded, 'count': '1,420+ Ads'},
    {'name': 'Mobile Phones', 'icon': Icons.smartphone_rounded, 'count': '3,890+ Ads'},
    {'name': 'Luxury Property', 'icon': Icons.apartment_rounded, 'count': '940+ Ads'},
    {'name': 'Electronics & Tech', 'icon': Icons.devices_other_rounded, 'count': '2,150+ Ads'},
    {'name': 'Bikes & Motorcycles', 'icon': Icons.two_wheeler_rounded, 'count': '870+ Ads'},
    {'name': 'Watches & Jewelry', 'icon': Icons.watch_outlined, 'count': '430+ Ads'},
    {'name': 'Home & Furniture', 'icon': Icons.chair_outlined, 'count': '1,120+ Ads'},
    {'name': 'Jobs & Services', 'icon': Icons.business_center_outlined, 'count': '680+ Ads'},
  ];

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('All Categories'),
      ),
      body: GridView.builder(
        padding: const EdgeInsets.all(AppTheme.space16),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          childAspectRatio: 1.15,
        ),
        itemCount: _catalogCategories.length,
        itemBuilder: (context, index) {
          final cat = _catalogCategories[index];
          return InkWell(
            borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
            onTap: () {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Browsing ${cat['name']} catalog...')),
              );
            },
            child: Container(
              padding: const EdgeInsets.all(AppTheme.space16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                border: Border.all(color: AppTheme.borderSubtle),
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.obsidian.withValues(alpha: 0.04),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: AppTheme.obsidian,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(cat['icon'] as IconData, color: AppTheme.champagne, size: 26),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    cat['name'] as String,
                    textAlign: TextAlign.center,
                    style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: AppTheme.obsidian),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    cat['count'] as String,
                    style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary, fontWeight: FontWeight.w500),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
