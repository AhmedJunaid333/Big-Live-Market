import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'core/widgets/nebula_glass_widgets.dart';
import 'features/categories/screens/categories_screen.dart';
import 'features/chat/screens/chat_list_screen.dart';
import 'features/listings/screens/listing_detail_screen.dart';
import 'features/listings/screens/post_ad_screen.dart';
import 'features/profile/screens/profile_screen.dart';
import 'features/splash/screens/splash_screen.dart';

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

final searchQueryProvider = StateProvider<String>((ref) => '');

final activeCategoryFilterProvider = StateProvider<String>((ref) => 'All');

final listingsFeedProvider = FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final query = ref.watch(searchQueryProvider);

  try {
    final response = await client.dio.get(
      query.isEmpty ? '/listings' : '/search?q=$query',
    );
    final data = response.data['data'] as List<dynamic>?;
    return data ?? [];
  } catch (e) {
    // Return sample offline listings if server temporarily unreachable
    return [
      {
        'id': 1,
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
        'title': '1 Kanal Luxury Designer House for Sale',
        'description': 'Spanish architecture, 5 master bedrooms, imported Italian fittings, lush lawn.',
        'price_minor': 9500000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Clifton Block 4, Karachi',
        'seller': {'name': 'Prime Estates', 'phone_e164': '+923219876543'}
      },
    ];
  }
});

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const ProviderScope(child: BigLiveApp()));
}

class BigLiveApp extends StatelessWidget {
  const BigLiveApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Big Live Marketplace',
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

  final List<String> _quickCategories = const [
    'All',
    'Motors',
    'Mobiles',
    'Property',
    'Tech',
  ];

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(listingsFeedProvider);
    final activeFilter = ref.watch(activeCategoryFilterProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Top App Bar (Flat - no fill, no border)
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 12),
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
                        const SizedBox(height: 4),
                        Text(
                          'Big Live',
                          style: AppTheme.displayLg,
                        ),
                      ],
                    ),
                    Row(
                      children: [
                        NebulaGlassIconButton(
                          icon: Icons.refresh_rounded,
                          color: AppTheme.primaryLavender,
                          onPressed: () {
                            ref.invalidate(listingsFeedProvider);
                          },
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Glass Search Input Bar
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 8),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                  child: Container(
                    decoration: BoxDecoration(
                      color: AppTheme.surfaceGlass,
                      borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                      border: Border.all(color: AppTheme.borderHairline, width: 1),
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
                    child: TextField(
                      controller: _searchController,
                      style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                      decoration: InputDecoration(
                        hintText: 'Search cars, mobiles, luxury villas...',
                        hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                        prefixIcon: const Icon(Icons.search_rounded, color: AppTheme.primaryLavender, size: 22),
                        suffixIcon: _searchController.text.isNotEmpty
                            ? IconButton(
                                icon: const Icon(Icons.clear_rounded, color: AppTheme.onSurfaceVariant, size: 18),
                                onPressed: () {
                                  _searchController.clear();
                                  ref.read(searchQueryProvider.notifier).state = '';
                                },
                              )
                            : null,
                        border: InputBorder.none,
                        enabledBorder: InputBorder.none,
                        focusedBorder: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                      onSubmitted: (query) {
                        ref.read(searchQueryProvider.notifier).state = query.trim();
                      },
                    ),
                  ),
                ),
              ),

              // Segmented Category Filter Tray
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 8),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: _quickCategories.map((category) {
                      final isSelected = activeFilter == category;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: GestureDetector(
                          onTap: () {
                            ref.read(activeCategoryFilterProvider.notifier).state = category;
                          },
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? Colors.white.withValues(alpha: 0.10)
                                  : Colors.transparent,
                              borderRadius: BorderRadius.circular(AppTheme.radiusSm),
                              border: Border.all(
                                color: isSelected ? AppTheme.borderHairline : Colors.transparent,
                              ),
                              boxShadow: isSelected
                                  ? [
                                      const BoxShadow(
                                        color: Color(0x1A000000),
                                        blurRadius: 4,
                                        offset: Offset(0, 1),
                                      ),
                                    ]
                                  : null,
                            ),
                            child: Text(
                              category,
                              style: AppTheme.bodyMd.copyWith(
                                color: isSelected ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
                                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                              ),
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ),

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

                          return NebulaGlassCard(
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
                                const Icon(
                                  Icons.chevron_right_rounded,
                                  color: AppTheme.onSurfaceMuted,
                                  size: 22,
                                ),
                              ],
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
