import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../models/category_taxonomy.dart';
import 'category_listings_screen.dart';
import 'subcategories_screen.dart';

final dynamicCategoriesProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/categories');
    final data = response.data['data'] as List<dynamic>?;
    if (data != null && data.isNotEmpty) {
      return data.map((item) {
        final id = item['id'] as int;
        final matched = CategoryTaxonomy.getCategoryById(id);
        return {
          'id': id,
          'name': item['name']?.toString() ?? matched.name,
          'icon': matched.icon,
          'count': '${item['children']?.length ?? matched.subcategories.length} Subcategories',
          'color': matched.tint,
        };
      }).toList();
    }
  } catch (_) {}
  return CategoriesScreen.defaultCategories;
});

class CategoriesScreen extends ConsumerWidget {
  const CategoriesScreen({super.key});

  static List<Map<String, dynamic>> get defaultCategories => CategoryTaxonomy.allCategories.map((c) {
    return {
      'id': c.id,
      'name': c.name,
      'icon': c.icon,
      'count': '${c.subcategories.length} Subcategories',
      'color': c.tint,
    };
  }).toList();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final categoriesAsync = ref.watch(dynamicCategoriesProvider);
    final categories = categoriesAsync.asData?.value ?? defaultCategories;

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Header
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'EXPLORE DEPARTMENTS',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Categories',
                          style: AppTheme.displayLg,
                        ),
                      ],
                    ),
                    NebulaGlassIconButton(
                      icon: Icons.refresh_rounded,
                      color: AppTheme.primaryLavender,
                      onPressed: () {
                        ref.invalidate(dynamicCategoriesProvider);
                      },
                    ),
                  ],
                ),
              ),

              // 2-Up Glass Category Grid
              Expanded(
                child: GridView.builder(
                  physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                  cacheExtent: 600,
                  padding: const EdgeInsets.fromLTRB(
                    AppTheme.gutter,
                    8,
                    AppTheme.gutter,
                    110, // Reserve padding to clear floating tab bar
                  ),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 14,
                    mainAxisSpacing: 14,
                    childAspectRatio: 0.88,
                  ),
                  itemCount: categories.length,
                  itemBuilder: (context, index) {
                    final cat = categories[index];
                    final color = cat['color'] as Color;
                    final catId = (cat['id'] ?? 1) as int;
                    final matched = CategoryTaxonomy.getCategoryById(catId);

                    return RepaintBoundary(
                      child: NebulaGlassCard(
                        borderRadius: AppTheme.radius2Xl,
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                        onTap: () {
                          if (matched.subcategories.isNotEmpty) {
                            Navigator.of(context).push(
                              MaterialPageRoute(
                                builder: (_) => SubcategoriesScreen(category: matched),
                              ),
                            );
                          } else {
                            Navigator.of(context).push(
                              MaterialPageRoute(
                                builder: (_) => CategoryListingsScreen(
                                  categoryId: catId,
                                  categoryName: cat['name'] as String,
                                  icon: cat['icon'] as IconData,
                                  accentColor: color,
                                ),
                              ),
                            );
                          }
                        },
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            TintedIconBubble(
                              icon: cat['icon'] as IconData,
                              accentColor: color,
                              size: 46,
                              iconSize: 24,
                            ),
                            const SizedBox(height: 10),
                            Flexible(
                              child: Text(
                                cat['name'] as String,
                                textAlign: TextAlign.center,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: AppTheme.bodyMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 13,
                                  height: 1.25,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              cat['count'] as String,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: AppTheme.caption.copyWith(
                                color: AppTheme.onSurfaceMuted,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
