import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'core/localization/app_strings.dart';
import 'core/widgets/nebula_glass_widgets.dart';
import 'features/categories/screens/categories_screen.dart';
import 'features/categories/widgets/olx_category_grid.dart';
import 'features/chat/screens/chat_list_screen.dart';
import 'features/listings/providers/favorites_provider.dart';
import 'features/listings/providers/filter_provider.dart';
import 'features/listings/screens/favorites_screen.dart';
import 'features/listings/screens/listing_detail_screen.dart';
import 'features/listings/screens/nearby_radar_screen.dart';
import 'features/listings/screens/post_ad_screen.dart';
import 'features/listings/widgets/filter_bottom_sheet.dart';
import 'features/profile/screens/profile_screen.dart';
import 'features/splash/screens/splash_screen.dart';

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

final searchQueryProvider = StateProvider<String>((ref) => '');

final activeCategoryFilterProvider = StateProvider<String>((ref) => 'All');

const Map<String, int?> categoryIdMap = {
  'All': null,
  'Motors': 1,
  'Mobiles': 3,
  'Property': 5,
  'Tech': 6,
};

final featuredListingsProvider = FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/listings?featured=1');
    final data = response.data['data'] as List<dynamic>?;
    return List<dynamic>.from(data ?? []);
  } catch (e) {
    return [
      {
        'id': 2,
        'title': 'Apple iPhone 15 Pro Max 256GB Titanium',
        'price_minor': 43000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Gulberg III, Lahore',
        'is_featured': true,
        'featured_plan': 'top_spotlight',
      },
      {
        'id': 1,
        'title': 'Toyota Corolla Altis Grande 1.8 2023',
        'price_minor': 850000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'DHA Phase 5, Lahore',
        'is_featured': true,
        'featured_plan': 'featured_urgent',
      },
    ];
  }
});

final listingsFeedProvider = FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final query = ref.watch(searchQueryProvider);
  final activeCategory = ref.watch(activeCategoryFilterProvider);
  final filter = ref.watch(filterProvider);
  final categoryId = categoryIdMap[activeCategory];

  List<dynamic> items = [];

  try {
    String endpoint;
    if (query.isNotEmpty) {
      endpoint = '/search?q=$query';
      if (categoryId != null) {
        endpoint += '&category_id=$categoryId';
      }
    } else if (categoryId != null) {
      endpoint = '/listings?category_id=$categoryId';
    } else {
      endpoint = '/listings';
    }

    final response = await client.dio.get(endpoint);
    final data = response.data['data'] as List<dynamic>?;
    items = List<dynamic>.from(data ?? []);
  } catch (e) {
    // Return sample offline listings filtered by category if applicable
    final allOffline = [
      {
        'id': 1,
        'category_id': 1,
        'title': 'Honda Civic RS Turbo 2022 Low Mileage',
        'description': 'First owner, bumper to bumper genuine, maintained through authorized dealership.',
        'price_minor': 850000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'DHA Phase 5, Lahore',
        'seller': {'name': 'Tariq Mehmood', 'phone_e164': '+923001234567'}
      },
      {
        'id': 2,
        'category_id': 3,
        'title': 'Apple iPhone 15 Pro Max 256GB Natural Titanium',
        'description': 'PTA approved, battery health 99%, complete original box and cable included.',
        'price_minor': 43000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'Blue Area, Islamabad',
        'seller': {'name': 'Apple Store PK', 'phone_e164': '+923337654321'}
      },
      {
        'id': 3,
        'category_id': 5,
        'title': '1 Kanal Luxury Designer House for Sale',
        'description': 'Spanish architecture, 5 master bedrooms, imported Italian fittings, lush lawn.',
        'price_minor': 9500000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Clifton Block 4, Karachi',
        'seller': {'name': 'Prime Estates', 'phone_e164': '+923219876543'}
      },
    ];

    if (categoryId != null) {
      items = allOffline.where((item) => item['category_id'] == categoryId).toList();
    } else {
      items = List<dynamic>.from(allOffline);
    }
  }

  // Apply Condition Filter
  if (filter.condition != null) {
    items = items.where((item) {
      if (item is! Map) return false;
      return item['condition_code']?.toString().toLowerCase() == filter.condition?.toLowerCase();
    }).toList();
  }

  // Apply City Filter
  if (filter.city != null) {
    items = items.where((item) {
      if (item is! Map) return false;
      final loc = item['location_label']?.toString().toLowerCase() ?? '';
      return loc.contains(filter.city!.toLowerCase());
    }).toList();
  }

  // Apply Price Filters
  if (filter.minPrice != null) {
    items = items.where((item) {
      if (item is! Map) return false;
      final price = ((item['price_minor'] ?? 0) as num) / 100;
      return price >= filter.minPrice!;
    }).toList();
  }

  if (filter.maxPrice != null) {
    items = items.where((item) {
      if (item is! Map) return false;
      final price = ((item['price_minor'] ?? 0) as num) / 100;
      return price <= filter.maxPrice!;
    }).toList();
  }

  // Apply Sorting
  if (filter.sortBy == 'price_asc') {
    items.sort((a, b) {
      final pa = (a is Map ? (a['price_minor'] ?? 0) as num : 0);
      final pb = (b is Map ? (b['price_minor'] ?? 0) as num : 0);
      return pa.compareTo(pb);
    });
  } else if (filter.sortBy == 'price_desc') {
    items.sort((a, b) {
      final pa = (a is Map ? (a['price_minor'] ?? 0) as num : 0);
      final pb = (b is Map ? (b['price_minor'] ?? 0) as num : 0);
      return pb.compareTo(pa);
    });
  }

  return items;
});

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const ProviderScope(child: BigLiveApp()));
}

class BigLiveApp extends ConsumerWidget {
  const BigLiveApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    ref.watch(languageProvider);
    return MaterialApp(
      title: AppStrings.appTitle,
      theme: AppTheme.darkTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: ThemeMode.dark,
      home: const SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}

class MainNavigationShell extends StatefulWidget {
  const MainNavigationShell({super.key});

  @override
  State<MainNavigationShell> createState() => _MainNavigationShellState();
}

class _MainNavigationShellState extends State<MainNavigationShell> {
  int _currentIndex = 0;

  final List<Widget> _screens = const [
    HomeFeedScreen(),
    CategoriesScreen(),
    PostAdScreen(),
    ChatListScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: Stack(
        children: [
          // Screen Viewport
          IndexedStack(
            index: _currentIndex,
            children: _screens,
          ),

          // Floating Glass Tab Bar anchored at bottom
          Positioned(
            left: 0,
            right: 0,
            bottom: 0,
            child: FloatingGlassTabBar(
              selectedIndex: _currentIndex,
              onTabSelected: (index) {
                setState(() {
                  _currentIndex = index;
                });
              },
              onCenterFabPressed: () {
                setState(() {
                  _currentIndex = 2; // Jump to Post Ad
                });
              },
            ),
          ),
        ],
      ),
    );
  }
}

class HomeFeedScreen extends ConsumerStatefulWidget {
  const HomeFeedScreen({super.key});

  @override
  ConsumerState<HomeFeedScreen> createState() => _HomeFeedScreenState();
}

class _HomeFeedScreenState extends ConsumerState<HomeFeedScreen> {
  final _searchController = TextEditingController();
  bool _isSearchVisible = false;

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(listingsFeedProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Sleek Cosmic Top App Bar with Search Button beside Radar
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 14, AppTheme.gutter, 8),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'NEBULA MARKETPLACE',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Big Live',
                          style: AppTheme.displayLg.copyWith(fontSize: 26),
                        ),
                      ],
                    ),
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Search Button beside Radar
                        NebulaGlassIconButton(
                          icon: _isSearchVisible ? Icons.close_rounded : Icons.search_rounded,
                          color: _isSearchVisible ? AppTheme.accentPink : AppTheme.primaryLavender,
                          tooltip: 'Search',
                          onPressed: () {
                            setState(() {
                              _isSearchVisible = !_isSearchVisible;
                              if (!_isSearchVisible) {
                                _searchController.clear();
                                ref.read(searchQueryProvider.notifier).state = '';
                              }
                            });
                          },
                        ),
                        const SizedBox(width: 6),
                        NebulaGlassIconButton(
                          icon: Icons.radar_rounded,
                          color: AppTheme.secondaryMint,
                          tooltip: 'Nearby Radar Deals',
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const NearbyRadarScreen()),
                            );
                          },
                        ),
                        const SizedBox(width: 6),
                        NebulaGlassIconButton(
                          icon: Icons.filter_list_rounded,
                          color: ref.watch(filterProvider).isActive ? AppTheme.accentCyan : AppTheme.onSurfaceVariant,
                          tooltip: 'Filters',
                          onPressed: () {
                            FilterBottomSheet.show(context);
                          },
                        ),
                        const SizedBox(width: 6),
                        NebulaGlassIconButton(
                          icon: Icons.favorite_border_rounded,
                          color: AppTheme.accentPink,
                          tooltip: 'Saved Favorites',
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                            );
                          },
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Expandable Search Bar (when user taps search button)
              if (_isSearchVisible)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 4),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                    child: Container(
                      decoration: BoxDecoration(
                        color: AppTheme.surfaceGlass,
                        borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                        border: Border.all(color: AppTheme.primaryLavender.withValues(alpha: 0.4), width: 1),
                      ),
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 0),
                      child: TextField(
                        controller: _searchController,
                        autofocus: true,
                        style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                        decoration: InputDecoration(
                          hintText: 'Search cars, mobiles, luxury villas...',
                          hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                          prefixIcon: const Icon(Icons.search_rounded, color: AppTheme.primaryLavender, size: 20),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear_rounded, color: AppTheme.onSurfaceVariant, size: 16),
                                  onPressed: () {
                                    _searchController.clear();
                                    ref.read(searchQueryProvider.notifier).state = '';
                                  },
                                )
                              : null,
                          border: InputBorder.none,
                          contentPadding: const EdgeInsets.symmetric(vertical: 10),
                        ),
                        onSubmitted: (query) {
                          ref.read(searchQueryProvider.notifier).state = query.trim();
                        },
                      ),
                    ),
                  ),
                ),

              // TOP RANKING FEATURED BANNERS (Moved to the Top immediately below Header)
              Consumer(
                builder: (context, ref, _) {
                  final featuredAsync = ref.watch(featuredListingsProvider);
                  return featuredAsync.maybeWhen(
                    data: (featuredItems) {
                      if (featuredItems.isEmpty) return const SizedBox.shrink();
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Padding(
                            padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 8, AppTheme.gutter, 6),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(3),
                                  decoration: BoxDecoration(
                                    color: AppTheme.accentAmber.withValues(alpha: 0.2),
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: const Icon(
                                    Icons.bolt_rounded,
                                    size: 14,
                                    color: AppTheme.accentAmber,
                                  ),
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  'FEATURED PROMOTIONS',
                                  style: AppTheme.labelUppercase.copyWith(
                                    letterSpacing: 1.0,
                                    fontSize: 11,
                                    color: AppTheme.accentAmber,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                const Spacer(),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                                    border: Border.all(color: AppTheme.accentAmber.withValues(alpha: 0.4)),
                                    color: AppTheme.accentAmber.withValues(alpha: 0.1),
                                  ),
                                  child: Text(
                                    'SPONSORED',
                                    style: AppTheme.labelSm.copyWith(
                                      color: AppTheme.accentAmber,
                                      fontSize: 9,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          // Compact, Sleek Banner Carousel (height reduced to 88px)
                          SizedBox(
                            height: 94,
                            child: ListView.separated(
                              physics: const BouncingScrollPhysics(),
                              padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter),
                              scrollDirection: Axis.horizontal,
                              itemCount: featuredItems.length,
                              separatorBuilder: (_, __) => const SizedBox(width: 10),
                              itemBuilder: (context, index) {
                                final f = featuredItems[index] as Map<String, dynamic>;
                                final priceMinor = (f['price_minor'] ?? 0) as num;
                                final currency = f['currency']?.toString() ?? 'PKR';
                                final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';

                                return RepaintBoundary(
                                  child: Container(
                                    width: 220,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                      gradient: LinearGradient(
                                        begin: Alignment.topLeft,
                                        end: Alignment.bottomRight,
                                        colors: [
                                          AppTheme.accentAmber.withValues(alpha: 0.20),
                                          AppTheme.backgroundMid.withValues(alpha: 0.85),
                                        ],
                                      ),
                                      border: Border.all(
                                        color: AppTheme.accentAmber.withValues(alpha: 0.45),
                                        width: 1.0,
                                      ),
                                    ),
                                    child: Material(
                                      color: Colors.transparent,
                                      child: InkWell(
                                        borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                        onTap: () {
                                          Navigator.of(context).push(
                                            MaterialPageRoute(
                                              builder: (_) => ListingDetailScreen(listing: f),
                                            ),
                                          );
                                        },
                                        child: Padding(
                                          padding: const EdgeInsets.all(10),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Row(
                                                children: [
                                                  Container(
                                                    padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
                                                    decoration: BoxDecoration(
                                                      color: AppTheme.accentAmber,
                                                      borderRadius: BorderRadius.circular(3),
                                                    ),
                                                    child: const Row(
                                                      mainAxisSize: MainAxisSize.min,
                                                      children: [
                                                        Icon(Icons.star_rounded, size: 10, color: Colors.black),
                                                        SizedBox(width: 2),
                                                        Text(
                                                          'BANNER',
                                                          style: TextStyle(
                                                            fontSize: 8,
                                                            fontWeight: FontWeight.w900,
                                                            color: Colors.black,
                                                          ),
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                  const Spacer(),
                                                  Text(
                                                    f['location_label']?.toString() ?? 'Pakistan',
                                                    style: AppTheme.caption.copyWith(
                                                      color: AppTheme.onSurfaceMuted,
                                                      fontSize: 9,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              Text(
                                                f['title']?.toString() ?? '',
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: AppTheme.bodySm.copyWith(
                                                  fontWeight: FontWeight.w700,
                                                  color: AppTheme.onSurface,
                                                  fontSize: 13,
                                                ),
                                              ),
                                              Text(
                                                formattedPrice,
                                                style: AppTheme.priceSm.copyWith(
                                                  color: AppTheme.accentAmber,
                                                  fontWeight: FontWeight.bold,
                                                  fontSize: 14,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
                          const SizedBox(height: 6),
                        ],
                      );
                    },
                    orElse: () => const SizedBox.shrink(),
                  );
                },
              ),

              // Modern OLX-Style Visual Category Buttons Grid
              const OlxCategoryGrid(),

              const SizedBox(height: 4),

              // Listings Feed
              Expanded(
                child: listingsAsync.when(
                  data: (listings) {
                    if (listings.isEmpty) {
                      return Center(
                        child: Padding(
                          padding: const EdgeInsets.all(AppTheme.gutter),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              TintedIconBubble(
                                icon: Icons.inventory_2_rounded,
                                accentColor: AppTheme.accentIndigo,
                                size: 64,
                                iconSize: 32,
                              ),
                              const SizedBox(height: AppTheme.space16),
                              Text(
                                'No active listings found',
                                style: AppTheme.headlineMd.copyWith(
                                  color: AppTheme.onSurface,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              const SizedBox(height: AppTheme.space8),
                              Text(
                                'Try another keyword or post an item for sale!',
                                textAlign: TextAlign.center,
                                style: AppTheme.bodySm.copyWith(
                                  color: AppTheme.onSurfaceMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }

                    return RefreshIndicator(
                      color: AppTheme.primaryLavender,
                      backgroundColor: AppTheme.backgroundMid,
                      onRefresh: () async {
                        ref.invalidate(listingsFeedProvider);
                      },
                      child: ListView.separated(
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        cacheExtent: 600,
                        padding: const EdgeInsets.fromLTRB(
                          AppTheme.gutter,
                          12,
                          AppTheme.gutter,
                          110, // Generous padding to clear floating glass tab bar
                        ),
                        itemCount: listings.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 16),
                        itemBuilder: (context, index) {
                          final item = listings[index] as Map<String, dynamic>;
                          final priceMinor = (item['price_minor'] ?? 0) as num;
                          final currency = item['currency']?.toString() ?? 'PKR';
                          final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
                          final location = item['location_label']?.toString() ?? 'Pakistan';
                          final condition = item['condition_code']?.toString() ?? 'used';
                          final isNew = condition.toLowerCase() == 'new';

                          return RepaintBoundary(
                            child: NebulaGlassCard(
                              padding: const EdgeInsets.all(16),
                              borderRadius: AppTheme.radius2Xl,
                              onTap: () {
                                Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) => ListingDetailScreen(listing: item),
                                  ),
                                );
                              },
                              child: Row(
                                children: [
                                  // Thumbnail with neon status badge
                                  Container(
                                    width: 88,
                                    height: 88,
                                    decoration: BoxDecoration(
                                      color: AppTheme.backgroundMid,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                      border: Border.all(color: AppTheme.borderHairline),
                                    ),
                                    child: Stack(
                                      alignment: Alignment.center,
                                      children: [
                                        Icon(
                                          Icons.photo_size_select_actual_rounded,
                                          color: AppTheme.primaryLavender.withValues(alpha: 0.6),
                                          size: 34,
                                        ),
                                        Positioned(
                                          bottom: 6,
                                          left: 6,
                                          child: NebulaBadgePill(
                                            label: condition,
                                            color: isNew ? AppTheme.accentMint : AppTheme.accentAmber,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 16),

                                  // Listing Info
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item['title']?.toString() ?? 'Untitled',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: AppTheme.bodyMd.copyWith(
                                            fontWeight: FontWeight.w700,
                                            color: AppTheme.onSurface,
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Row(
                                          children: [
                                            const Icon(
                                              Icons.location_on_rounded,
                                              size: 13,
                                              color: AppTheme.onSurfaceMuted,
                                            ),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                location,
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: AppTheme.bodySm.copyWith(
                                                  color: AppTheme.onSurfaceVariant,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 8),
                                        Text(
                                          formattedPrice,
                                          style: AppTheme.numeralLg.copyWith(
                                            color: AppTheme.primaryLavender,
                                            shadows: const [
                                              Shadow(
                                                color: Color(0x66D8B4FE),
                                                blurRadius: 8,
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  IconButton(
                                    icon: Icon(
                                      ref.watch(favoritesProvider).contains((item['id'] as num?)?.toInt())
                                          ? Icons.favorite_rounded
                                          : Icons.favorite_border_rounded,
                                      color: ref.watch(favoritesProvider).contains((item['id'] as num?)?.toInt())
                                          ? AppTheme.accentPink
                                          : AppTheme.onSurfaceMuted,
                                      size: 20,
                                    ),
                                    onPressed: () {
                                      final id = (item['id'] as num?)?.toInt() ?? 0;
                                      ref.read(favoritesProvider.notifier).toggleFavorite(id);
                                    },
                                  ),
                                  const Icon(
                                    Icons.chevron_right_rounded,
                                    color: AppTheme.onSurfaceMuted,
                                    size: 20,
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    );
                  },
                  loading: () => const Center(
                    child: CircularProgressIndicator(
                      color: AppTheme.primaryLavender,
                    ),
                  ),
                  error: (error, _) => Center(
                    child: Text(
                      'Error loading catalog: $error',
                      style: AppTheme.bodyMd.copyWith(color: AppTheme.error),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
