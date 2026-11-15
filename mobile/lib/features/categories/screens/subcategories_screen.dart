import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../models/category_taxonomy.dart';
import 'category_listings_screen.dart';

/// Screen displaying the complete, deep subcategories list with rich 3D icons & interactive tags
class SubcategoriesScreen extends StatelessWidget {
  final MainCategoryItem category;

  const SubcategoriesScreen({
    super.key,
    required this.category,
  });

  @override
  Widget build(BuildContext context) {
    final subcategories = category.subcategories;

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic App Bar
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 12, AppTheme.gutter, 8),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    const SizedBox(width: 14),
                    TintedIconBubble(
                      icon: category.icon,
                      accentColor: category.tint,
                      size: 44,
                      iconSize: 22,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'SELECT SUBCATEGORY',
                            style: AppTheme.labelUppercase.copyWith(
                              color: category.tint,
                              letterSpacing: 1.2,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            category.name,
                            style: AppTheme.headlineMd.copyWith(
                              fontWeight: FontWeight.w700,
                              color: AppTheme.onSurface,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // "View All in Category" Luxury Card
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 6),
                child: InkWell(
                  onTap: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => CategoryListingsScreen(
                          categoryId: category.id,
                          categoryName: category.name,
                          icon: category.icon,
                          accentColor: category.tint,
                          selectedSubcategory: null,
                        ),
                      ),
                    );
                  },
                  borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          category.tint.withValues(alpha: 0.22),
                          AppTheme.surfaceGlass,
                        ],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                      border: Border.all(
                        color: category.tint.withValues(alpha: 0.45),
                        width: 1.2,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: category.tint.withValues(alpha: 0.15),
                          blurRadius: 12,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 40,
                          height: 40,
                          decoration: BoxDecoration(
                            color: category.tint.withValues(alpha: 0.25),
                            shape: BoxShape.circle,
                          ),
                          child: Icon(
                            Icons.grid_view_rounded,
                            color: category.tint,
                            size: 20,
                          ),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'View All in ${category.name}',
                                style: AppTheme.bodyMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                              Text(
                                'Browse all ${subcategories.length} subcategories combined',
                                style: AppTheme.caption.copyWith(
                                  color: AppTheme.onSurfaceMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                        Icon(
                          Icons.arrow_forward_ios_rounded,
                          color: category.tint,
                          size: 16,
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 6),

              // Subcategories List Header
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter + 4, vertical: 6),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'DEEP SUBCATEGORIES',
                      style: AppTheme.labelUppercase.copyWith(
                        color: AppTheme.onSurfaceMuted,
                        fontSize: 11,
                      ),
                    ),
                    Text(
                      '${subcategories.length} AVAILABLE',
                      style: AppTheme.caption.copyWith(
                        color: category.tint,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),

              // Subcategories Vertical List with Rich 3D Icons & Detailed Tag Pills
              Expanded(
                child: ListView.separated(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 4, AppTheme.gutter, 30),
                  itemCount: subcategories.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final sub = subcategories[index];

                    return RepaintBoundary(
                      child: InkWell(
                        onTap: () {
                          Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => CategoryListingsScreen(
                                categoryId: category.id,
                                categoryName: category.name,
                                icon: sub.icon,
                                accentColor: category.tint,
                                selectedSubcategory: sub,
                              ),
                            ),
                          );
                        },
                        borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                        child: Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: AppTheme.surfaceGlass,
                            borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                            border: Border.all(
                              color: AppTheme.borderHairline,
                              width: 1.0,
                            ),
                            boxShadow: const [
                              BoxShadow(
                                color: Color(0x33000000),
                                blurRadius: 8,
                                offset: Offset(0, 3),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Top Row: Icon, Title, Count, Chevron
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: [
                                  // 3D Visual Icon Badge
                                  _buildSubcategoryBadge(sub),
                                  const SizedBox(width: 14),

                                  // Name & Subtitle
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Expanded(
                                              child: Text(
                                                sub.name,
                                                style: AppTheme.bodyMd.copyWith(
                                                  fontWeight: FontWeight.w700,
                                                  fontSize: 15,
                                                  color: AppTheme.onSurface,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        if (sub.subtitle != null) ...[
                                          const SizedBox(height: 2),
                                          Text(
                                            sub.subtitle!,
                                            style: AppTheme.caption.copyWith(
                                              color: AppTheme.onSurfaceMuted,
                                              fontSize: 12,
                                            ),
                                            maxLines: 2,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ],
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 10),

                                  // Count Pill & Arrow
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: AppTheme.backgroundMid,
                                          borderRadius: BorderRadius.circular(12),
                                          border: Border.all(color: AppTheme.borderHairline),
                                        ),
                                        child: Text(
                                          '${sub.sampleCount}+ Ads',
                                          style: AppTheme.caption.copyWith(
                                            color: category.tint,
                                            fontWeight: FontWeight.w700,
                                            fontSize: 10,
                                          ),
                                        ),
                                      ),
                                      const SizedBox(height: 6),
                                      const Icon(
                                        Icons.chevron_right_rounded,
                                        color: AppTheme.onSurfaceMuted,
                                        size: 20,
                                      ),
                                    ],
                                  ),
                                ],
                              ),

                              // Detailed Sub-Tags Wrap (Clickable filters)
                              if (sub.tags.isNotEmpty) ...[
                                const SizedBox(height: 12),
                                const Divider(height: 1, color: AppTheme.borderHairline),
                                const SizedBox(height: 10),
                                Wrap(
                                  spacing: 6,
                                  runSpacing: 6,
                                  children: sub.tags.map((tag) {
                                    return InkWell(
                                      onTap: () {
                                        Navigator.of(context).push(
                                          MaterialPageRoute(
                                            builder: (_) => CategoryListingsScreen(
                                              categoryId: category.id,
                                              categoryName: category.name,
                                              icon: sub.icon,
                                              accentColor: category.tint,
                                              selectedSubcategory: sub,
                                              initialSearchQuery: tag,
                                            ),
                                          ),
                                        );
                                      },
                                      borderRadius: BorderRadius.circular(14),
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                        decoration: BoxDecoration(
                                          color: category.tint.withValues(alpha: 0.10),
                                          borderRadius: BorderRadius.circular(14),
                                          border: Border.all(
                                            color: category.tint.withValues(alpha: 0.28),
                                            width: 0.8,
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Container(
                                              width: 4,
                                              height: 4,
                                              decoration: BoxDecoration(
                                                color: category.tint,
                                                shape: BoxShape.circle,
                                              ),
                                            ),
                                            const SizedBox(width: 6),
                                            Text(
                                              tag,
                                              style: AppTheme.caption.copyWith(
                                                color: AppTheme.onSurface,
                                                fontWeight: FontWeight.w600,
                                                fontSize: 11,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    );
                                  }).toList(),
                                ),
                              ],
                            ],
                          ),
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

  Widget _buildSubcategoryBadge(SubcategoryItem sub) {
    if (sub.imageAsset != null && sub.imageAsset!.isNotEmpty) {
      return Container(
        width: 52,
        height: 52,
        padding: const EdgeInsets.all(5),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFF222B3D), Color(0xFF141923)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: category.tint.withValues(alpha: 0.50),
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: category.tint.withValues(alpha: 0.20),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Center(
          child: Image.asset(
            sub.imageAsset!,
            width: 40,
            height: 40,
            fit: BoxFit.contain,
            errorBuilder: (_, __, ___) => _buildFallbackVector(sub),
          ),
        ),
      );
    }

    return _buildFallbackVector(sub);
  }

  Widget _buildFallbackVector(SubcategoryItem sub) {
    return Container(
      width: 52,
      height: 52,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            category.tint.withValues(alpha: 0.22),
            const Color(0xFF161C28),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: category.tint.withValues(alpha: 0.40),
          width: 1.0,
        ),
        boxShadow: [
          BoxShadow(
            color: category.tint.withValues(alpha: 0.15),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Center(
        child: Icon(
          sub.icon,
          color: category.tint,
          size: 26,
        ),
      ),
    );
  }
}
