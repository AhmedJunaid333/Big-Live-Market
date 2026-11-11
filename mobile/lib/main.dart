import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'features/categories/screens/categories_screen.dart';
import 'features/chat/screens/chat_list_screen.dart';
import 'features/listings/screens/listing_detail_screen.dart';
import 'features/listings/screens/post_ad_screen.dart';
import 'features/profile/screens/profile_screen.dart';

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

final searchQueryProvider = StateProvider<String>((ref) => '');

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
      theme: AppTheme.lightTheme,
      home: const MainNavigationShell(),
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
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home, color: AppTheme.champagne),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.grid_view_outlined),
            selectedIcon: Icon(Icons.grid_view_rounded, color: AppTheme.champagne),
            label: 'Categories',
          ),
          NavigationDestination(
            icon: Icon(Icons.add_circle_outline, color: AppTheme.champagne, size: 28),
            selectedIcon: Icon(Icons.add_circle, color: AppTheme.champagne, size: 28),
            label: 'Sell',
          ),
          NavigationDestination(
            icon: Icon(Icons.chat_bubble_outline),
            selectedIcon: Icon(Icons.chat_bubble, color: AppTheme.champagne),
            label: 'Chats',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person, color: AppTheme.champagne),
            label: 'Profile',
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
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                color: AppTheme.champagne,
                borderRadius: BorderRadius.circular(8),
              ),
              alignment: Alignment.center,
              child: const Text(
                'B',
                style: TextStyle(
                  color: AppTheme.obsidian,
                  fontWeight: FontWeight.w900,
                  fontSize: 18,
                ),
              ),
            ),
            const SizedBox(width: 10),
            const Expanded(
              child: Text(
                'Big Live Marketplace',
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: AppTheme.champagne),
            onPressed: () {
              ref.invalidate(listingsFeedProvider);
            },
          ),
        ],
      ),
      body: Column(
        children: [
          // Search & Filter Header
          Container(
            padding: const EdgeInsets.all(AppTheme.space16),
            color: AppTheme.obsidian,
            child: TextField(
              controller: _searchController,
              style: const TextStyle(color: Colors.white, fontSize: 14),
              decoration: InputDecoration(
                hintText: 'Search cars, mobiles, luxury villas...',
                hintStyle: const TextStyle(color: Colors.white54, fontSize: 13),
                prefixIcon: const Icon(Icons.search, color: AppTheme.champagne),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, color: Colors.white70, size: 18),
                        onPressed: () {
                          _searchController.clear();
                          ref.read(searchQueryProvider.notifier).state = '';
                        },
                      )
                    : null,
                filled: true,
                fillColor: AppTheme.slateCard,
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppTheme.borderGold),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppTheme.borderGold),
                ),
              ),
              onSubmitted: (query) {
                ref.read(searchQueryProvider.notifier).state = query.trim();
              },
            ),
          ),

          // Listings List
          Expanded(
            child: listingsAsync.when(
              data: (listings) {
                if (listings.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.inventory_2_outlined, size: 64, color: AppTheme.textSecondary.withValues(alpha: 0.5)),
                        const SizedBox(height: AppTheme.space16),
                        const Text(
                          'No active listings found',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppTheme.textPrimary),
                        ),
                        const SizedBox(height: AppTheme.space8),
                        const Text(
                          'Try another keyword or post an item for sale!',
                          style: TextStyle(color: AppTheme.textSecondary),
                        ),
                      ],
                    ),
                  );
                }

                return RefreshIndicator(
                  color: AppTheme.champagne,
                  onRefresh: () async {
                    ref.invalidate(listingsFeedProvider);
                  },
                  child: ListView.separated(
                    padding: const EdgeInsets.all(AppTheme.space16),
                    itemCount: listings.length,
                    separatorBuilder: (_, __) => const SizedBox(height: AppTheme.space12),
                    itemBuilder: (context, index) {
                      final item = listings[index] as Map<String, dynamic>;
                      final priceMinor = (item['price_minor'] ?? 0) as num;
                      final currency = item['currency']?.toString() ?? 'PKR';
                      final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
                      final location = item['location_label']?.toString() ?? 'Pakistan';
                      final condition = item['condition_code']?.toString() ?? 'used';

                      return InkWell(
                        borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                        onTap: () {
                          Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => ListingDetailScreen(listing: item),
                            ),
                          );
                        },
                        child: Container(
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                            border: Border.all(color: AppTheme.borderSubtle),
                            boxShadow: [
                              BoxShadow(
                                color: AppTheme.obsidian.withValues(alpha: 0.04),
                                blurRadius: 16,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Padding(
                            padding: const EdgeInsets.all(AppTheme.space12),
                            child: Row(
                              children: [
                                Container(
                                  width: 80,
                                  height: 80,
                                  decoration: BoxDecoration(
                                    color: AppTheme.obsidian,
                                    borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
                                  ),
                                  child: Stack(
                                    alignment: Alignment.center,
                                    children: [
                                      const Icon(Icons.photo_outlined, color: AppTheme.champagne, size: 32),
                                      Positioned(
                                        bottom: 4,
                                        left: 4,
                                        child: Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                                          decoration: BoxDecoration(
                                            color: Colors.black.withValues(alpha: 0.6),
                                            borderRadius: BorderRadius.circular(4),
                                          ),
                                          child: Text(
                                            condition.toUpperCase(),
                                            style: const TextStyle(color: AppTheme.champagne, fontSize: 8, fontWeight: FontWeight.bold),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: AppTheme.space12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        item['title']?.toString() ?? 'Untitled',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        location,
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(color: AppTheme.textSecondary, fontSize: 12),
                                      ),
                                      const SizedBox(height: 6),
                                      Text(
                                        formattedPrice,
                                        style: const TextStyle(
                                          color: AppTheme.champagneDark,
                                          fontWeight: FontWeight.w900,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.chevron_right, color: AppTheme.textSecondary),
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
              loading: () => const Center(child: CircularProgressIndicator(color: AppTheme.champagne)),
              error: (error, _) => Center(
                child: Text('Error loading catalog: $error'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
