import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'core/localization/app_strings.dart';
import 'core/widgets/nebula_glass_widgets.dart';
import 'features/categories/widgets/olx_category_grid.dart';
import 'features/chat/screens/chat_list_screen.dart';
import 'features/listings/providers/favorites_provider.dart';
import 'features/listings/providers/filter_provider.dart';
import 'features/listings/screens/favorites_screen.dart';
import 'features/listings/screens/listing_detail_screen.dart';
import 'features/listings/screens/my_listings_screen.dart';
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
    ChatListScreen(),
    PostAdScreen(),
    MyListingsScreen(),
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
              // OLX-Style Top Department Selector (All, Motors, Property)
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 10, AppTheme.gutter, 6),
                child: Row(
                  children: [
                    // Big Live (All)
                    Expanded(
                      flex: 4,
                      child: GestureDetector(
                        onTap: () {
                          ref.read(activeCategoryFilterProvider.notifier).state = 'All';
                        },
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 180),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          decoration: BoxDecoration(
                            color: ref.watch(activeCategoryFilterProvider) == 'All'
                                ? const Color(0xFF1E293B)
                                : const Color(0xFF131722),
                            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                            border: Border.all(
                              color: ref.watch(activeCategoryFilterProvider) == 'All'
                                  ? const Color(0xFF38BDF8)
                                  : const Color(0xFF282F42),
                              width: 1.2,
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                'assets/icons/app_logo_mark.png',
                                width: 22,
                                height: 22,
                                fit: BoxFit.contain,
                              ),
                              const SizedBox(width: 6),
                              const Text(
                                'Big Live',
                                style: TextStyle(
                                  fontWeight: FontWeight.w800,
                                  fontSize: 13,
                                  color: Colors.white,
                                  letterSpacing: 0.3,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),

                    // Motors
                    Expanded(
                      flex: 4,
                      child: GestureDetector(
                        onTap: () {
                          ref.read(activeCategoryFilterProvider.notifier).state = 'Motors';
                        },
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 180),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          decoration: BoxDecoration(
                            color: ref.watch(activeCategoryFilterProvider) == 'Motors'
                                ? const Color(0xFF1E293B)
                                : const Color(0xFF131722),
                            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                            border: Border.all(
                              color: ref.watch(activeCategoryFilterProvider) == 'Motors'
                                  ? const Color(0xFF38BDF8)
                                  : const Color(0xFF282F42),
                              width: 1.2,
                            ),
                          ),
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.directions_car_filled_rounded, size: 16, color: Color(0xFF94A3B8)),
                              SizedBox(width: 6),
                              Text(
                                'Motors',
                                style: TextStyle(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 12.5,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),

                    // Property
                    Expanded(
                      flex: 4,
                      child: GestureDetector(
                        onTap: () {
                          ref.read(activeCategoryFilterProvider.notifier).state = 'Property';
                        },
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 180),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          decoration: BoxDecoration(
                            color: ref.watch(activeCategoryFilterProvider) == 'Property'
                                ? const Color(0xFF1E293B)
                                : const Color(0xFF131722),
                            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                            border: Border.all(
                              color: ref.watch(activeCategoryFilterProvider) == 'Property'
                                  ? const Color(0xFF38BDF8)
                                  : const Color(0xFF282F42),
                              width: 1.2,
                            ),
                          ),
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.home_work_rounded, size: 16, color: Color(0xFFF87171)),
                              SizedBox(width: 6),
                              Text(
                                'Property',
                                style: TextStyle(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 12.5,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Search Bar & Action Buttons (Classic, prominent & flexible)
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 4, AppTheme.gutter, 4),
                child: Row(
                  children: [
                    Expanded(
                      child: Container(
                        height: 44,
                        decoration: BoxDecoration(
                          color: const Color(0xFF161A26),
                          borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                          border: Border.all(color: const Color(0xFF2A3144), width: 1),
                        ),
                        child: Row(
                          children: [
                            const SizedBox(width: 10),
                            const Icon(Icons.search_rounded, color: Color(0xFF94A3B8), size: 20),
                            const SizedBox(width: 8),
                            Expanded(
                              child: TextField(
                                controller: _searchController,
                                style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface, fontSize: 13),
                                decoration: const InputDecoration(
                                  hintText: 'Search cars, mobiles, jobs...',
                                  hintStyle: TextStyle(color: Color(0xFF64748B), fontSize: 13),
                                  border: InputBorder.none,
                                  isDense: true,
                                  contentPadding: EdgeInsets.symmetric(vertical: 10),
                                ),
                                onChanged: (query) {
                                  ref.read(searchQueryProvider.notifier).state = query.trim();
                                },
                              ),
                            ),
                            if (_searchController.text.isNotEmpty)
                              GestureDetector(
                                onTap: () {
                                  _searchController.clear();
                                  ref.read(searchQueryProvider.notifier).state = '';
                                  setState(() {});
                                },
                                child: const Padding(
                                  padding: EdgeInsets.symmetric(horizontal: 8),
                                  child: Icon(Icons.close_rounded, color: AppTheme.onSurfaceMuted, size: 16),
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    // Nearby Radar Button
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
                    // Saved Favorites Button
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
              ),

              // Location Selector Line (e.g. 📍 Pakistan ∨)
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 4),
                child: Row(
                  children: [
                    const Icon(Icons.location_on_rounded, size: 14, color: Color(0xFF38BDF8)),
                    const SizedBox(width: 4),
                    PopupMenuButton<String>(
                      initialValue: ref.watch(filterProvider).city ?? 'Pakistan',
                      onSelected: (city) {
                        ref.read(filterProvider.notifier).setCity(city == 'Pakistan' ? null : city);
                      },
                      color: AppTheme.backgroundMid,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                        side: const BorderSide(color: AppTheme.borderHairline),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            ref.watch(filterProvider).city ?? 'Pakistan',
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(width: 4),
                          const Icon(Icons.keyboard_arrow_down_rounded, size: 16, color: Color(0xFF94A3B8)),
                        ],
                      ),
                      itemBuilder: (context) => [
                        const PopupMenuItem(value: 'Pakistan', child: Text('All Pakistan')),
                        const PopupMenuItem(value: 'Lahore', child: Text('Lahore')),
                        const PopupMenuItem(value: 'Karachi', child: Text('Karachi')),
                        const PopupMenuItem(value: 'Islamabad', child: Text('Islamabad')),
                        const PopupMenuItem(value: 'Rawalpindi', child: Text('Rawalpindi')),
                        const PopupMenuItem(value: 'Faisalabad', child: Text('Faisalabad')),
                        const PopupMenuItem(value: 'Multan', child: Text('Multan')),
                        const PopupMenuItem(value: 'Peshawar', child: Text('Peshawar')),
                      ],
                    ),
                    const Spacer(),
                    // Filter Sheet Trigger
                    GestureDetector(
                      onTap: () => FilterBottomSheet.show(context),
                      child: Row(
                        children: [
                          Icon(
                            Icons.tune_rounded,
                            size: 14,
                            color: ref.watch(filterProvider).isActive ? const Color(0xFF38BDF8) : const Color(0xFF94A3B8),
                          ),
                          const SizedBox(width: 4),
                          Text(
                            'Filters',
                            style: TextStyle(
                              fontSize: 11,
                              color: ref.watch(filterProvider).isActive ? const Color(0xFF38BDF8) : const Color(0xFF94A3B8),
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
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
                      color: const Color(0xFF38BDF8),
                      backgroundColor: AppTheme.backgroundMid,
                      onRefresh: () async {
                        ref.invalidate(listingsFeedProvider);
                      },
                      child: CustomScrollView(
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        slivers: [
                          // Section Header
                          SliverToBoxAdapter(
                            child: Padding(
                              padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 10, AppTheme.gutter, 8),
                              child: Row(
                                children: [
                                  Container(
                                    width: 4,
                                    height: 14,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF38BDF8),
                                      borderRadius: BorderRadius.circular(2),
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  Text(
                                    'FRESH RECOMMENDATIONS',
                                    style: AppTheme.labelUppercase.copyWith(
                                      letterSpacing: 1.1,
                                      fontSize: 11,
                                      color: const Color(0xFFCBD5E1),
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                  const Spacer(),
                                  Text(
                                    '${listings.length} Items',
                                    style: const TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: Color(0xFF94A3B8),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),

                          // 2-Column Photo Thumbnail Grid (OLX Style)
                          SliverPadding(
                            padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 4, AppTheme.gutter, 110),
                            sliver: SliverGrid(
                              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                                crossAxisCount: 2,
                                childAspectRatio: 0.65,
                                crossAxisSpacing: 12,
                                mainAxisSpacing: 12,
                              ),
                              delegate: SliverChildBuilderDelegate(
                                (context, index) {
                                  final item = listings[index] as Map<String, dynamic>;
                                  final priceMinor = (item['price_minor'] ?? 0) as num;
                                  final currency = item['currency']?.toString() ?? 'PKR';
                                  final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
                                  final location = item['location_label']?.toString() ?? 'Pakistan';
                                  final condition = item['condition_code']?.toString() ?? 'used';
                                  final isNew = condition.toLowerCase() == 'new';
                                  final categoryId = (item['category_id'] as num?)?.toInt();
                                  final isFav = ref.watch(favoritesProvider).contains((item['id'] as num?)?.toInt());

                                  // Choose matching high-def photo asset for thumbnail
                                  String? assetThumb;
                                  if (categoryId == 1) {
                                    assetThumb = 'assets/icons/cat_vehicles.png';
                                  } else if (categoryId == 3) {
                                    assetThumb = 'assets/icons/cat_mobiles.png';
                                  } else if (categoryId == 5) {
                                    assetThumb = 'assets/icons/cat_property_sale.png';
                                  } else if (categoryId == 6) {
                                    assetThumb = 'assets/icons/cat_electronics.png';
                                  }

                                  return RepaintBoundary(
                                    child: Material(
                                      color: Colors.transparent,
                                      child: InkWell(
                                        borderRadius: BorderRadius.circular(14),
                                        onTap: () {
                                          Navigator.of(context).push(
                                            MaterialPageRoute(
                                              builder: (_) => ListingDetailScreen(listing: item),
                                            ),
                                          );
                                        },
                                        child: Container(
                                          decoration: BoxDecoration(
                                            color: const Color(0xFF161A26),
                                            borderRadius: BorderRadius.circular(14),
                                            border: Border.all(
                                              color: const Color(0xFF282F42),
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
                                              // Large Photo Thumbnail with Badge & Heart
                                              ClipRRect(
                                                borderRadius: const BorderRadius.vertical(top: Radius.circular(13)),
                                                child: Container(
                                                  height: 124,
                                                  width: double.infinity,
                                                  color: const Color(0xFF1A1F2D),
                                                  child: Stack(
                                                    fit: StackFit.expand,
                                                    children: [
                                                      // Photo Asset or Network Image
                                                      if (assetThumb != null)
                                                        Image.asset(
                                                          assetThumb,
                                                          fit: BoxFit.cover,
                                                        )
                                                      else
                                                        Center(
                                                          child: Icon(
                                                            Icons.photo_size_select_actual_rounded,
                                                            color: AppTheme.primaryLavender.withValues(alpha: 0.5),
                                                            size: 38,
                                                          ),
                                                        ),

                                                      // Subtle bottom gradient overlay for contrast
                                                      Positioned(
                                                        left: 0,
                                                        right: 0,
                                                        bottom: 0,
                                                        height: 36,
                                                        child: Container(
                                                          decoration: BoxDecoration(
                                                            gradient: LinearGradient(
                                                              begin: Alignment.topCenter,
                                                              end: Alignment.bottomCenter,
                                                              colors: [
                                                                Colors.transparent,
                                                                Colors.black.withValues(alpha: 0.6),
                                                              ],
                                                            ),
                                                          ),
                                                        ),
                                                      ),

                                                      // Condition Badge (top left)
                                                      Positioned(
                                                        top: 8,
                                                        left: 8,
                                                        child: Container(
                                                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2.5),
                                                          decoration: BoxDecoration(
                                                            color: isNew
                                                                ? const Color(0xE610B981) // Green
                                                                : const Color(0xE6F59E0B), // Amber
                                                            borderRadius: BorderRadius.circular(4),
                                                          ),
                                                          child: Text(
                                                            isNew ? 'NEW' : 'USED',
                                                            style: const TextStyle(
                                                              fontSize: 9,
                                                              fontWeight: FontWeight.w900,
                                                              color: Colors.white,
                                                              letterSpacing: 0.5,
                                                            ),
                                                          ),
                                                        ),
                                                      ),

                                                      // Favorite Heart Button (top right)
                                                      Positioned(
                                                        top: 4,
                                                        right: 4,
                                                        child: GestureDetector(
                                                          onTap: () {
                                                            final id = (item['id'] as num?)?.toInt() ?? 0;
                                                            ref.read(favoritesProvider.notifier).toggleFavorite(id);
                                                          },
                                                          child: Container(
                                                            padding: const EdgeInsets.all(6),
                                                            decoration: BoxDecoration(
                                                              shape: BoxShape.circle,
                                                              color: Colors.black.withValues(alpha: 0.5),
                                                            ),
                                                            child: Icon(
                                                              isFav ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                                                              size: 16,
                                                              color: isFav ? AppTheme.accentPink : Colors.white,
                                                            ),
                                                          ),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                              ),

                                              // Listing Information below Thumbnail
                                              Padding(
                                                padding: const EdgeInsets.fromLTRB(10, 8, 10, 8),
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    // Price in bold striking color
                                                    Text(
                                                      formattedPrice,
                                                      style: const TextStyle(
                                                        fontSize: 15,
                                                        fontWeight: FontWeight.w900,
                                                        color: Color(0xFF38BDF8),
                                                        letterSpacing: -0.2,
                                                      ),
                                                      maxLines: 1,
                                                      overflow: TextOverflow.ellipsis,
                                                    ),
                                                    const SizedBox(height: 3),

                                                    // Title (2 lines)
                                                    SizedBox(
                                                      height: 32,
                                                      child: Text(
                                                        item['title']?.toString() ?? 'Untitled',
                                                        maxLines: 2,
                                                        overflow: TextOverflow.ellipsis,
                                                        style: const TextStyle(
                                                          fontSize: 12,
                                                          fontWeight: FontWeight.w600,
                                                          color: Colors.white,
                                                          height: 1.25,
                                                        ),
                                                      ),
                                                    ),
                                                    const SizedBox(height: 4),

                                                    // Location
                                                    Row(
                                                      children: [
                                                        const Icon(
                                                          Icons.location_on_rounded,
                                                          size: 11,
                                                          color: Color(0xFF94A3B8),
                                                        ),
                                                        const SizedBox(width: 3),
                                                        Expanded(
                                                          child: Text(
                                                            location,
                                                            maxLines: 1,
                                                            overflow: TextOverflow.ellipsis,
                                                            style: const TextStyle(
                                                              fontSize: 10,
                                                              color: Color(0xFF94A3B8),
                                                            ),
                                                          ),
                                                        ),
                                                      ],
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ),
                                    ),
                                  );
                                },
                                childCount: listings.length,
                              ),
                            ),
                          ),
                        ],
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
