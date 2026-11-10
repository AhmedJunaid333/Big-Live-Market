import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'features/profile/screens/profile_screen.dart';

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

final listingsFeedProvider = FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/listings');
    final data = response.data['data'] as List<dynamic>?;
    return data ?? [];
  } catch (e) {
    return [];
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
    PlaceholderScreen(title: 'Categories', icon: Icons.grid_view_rounded),
    PlaceholderScreen(title: 'Sell an Item', icon: Icons.add_circle_outline_rounded),
    PlaceholderScreen(title: 'Chats', icon: Icons.chat_bubble_outline_rounded),
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
            selectedIcon: Icon(Icons.home, color: AppTheme.brandBlue),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.grid_view_outlined),
            selectedIcon: Icon(Icons.grid_view_rounded, color: AppTheme.brandBlue),
            label: 'Categories',
          ),
          NavigationDestination(
            icon: Icon(Icons.add_circle_outline, color: AppTheme.accentAmber, size: 28),
            selectedIcon: Icon(Icons.add_circle, color: AppTheme.accentAmber, size: 28),
            label: 'Sell',
          ),
          NavigationDestination(
            icon: Icon(Icons.chat_bubble_outline),
            selectedIcon: Icon(Icons.chat_bubble, color: AppTheme.brandBlue),
            label: 'Chats',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person, color: AppTheme.brandBlue),
            label: 'Profile',
          ),
        ],
      ),
    );
  }
}

class HomeFeedScreen extends ConsumerWidget {
  const HomeFeedScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final listingsAsync = ref.watch(listingsFeedProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Big Live Marketplace'),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none_outlined),
            onPressed: () {},
          ),
        ],
      ),
      body: listingsAsync.when(
        data: (listings) {
          if (listings.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.inventory_2_outlined, size: 64, color: AppTheme.textSecondary.withOpacity(0.5)),
                  const SizedBox(height: AppTheme.space16),
                  const Text(
                    'No active listings found',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: AppTheme.textPrimary),
                  ),
                  const SizedBox(height: AppTheme.space8),
                  const Text(
                    'Be the first to post an item for sale!',
                    style: TextStyle(color: AppTheme.textSecondary),
                  ),
                ],
              ),
            );
          }

          return ListView.separated(
            padding: const EdgeInsets.all(AppTheme.space16),
            itemCount: listings.length,
            separatorBuilder: (_, __) => const SizedBox(height: AppTheme.space12),
            itemBuilder: (context, index) {
              final item = listings[index] as Map<String, dynamic>;
              final priceMinor = (item['price_minor'] ?? 0) as num;
              final currency = item['currency']?.toString() ?? 'PKR';
              final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';

              return Card(
                child: ListTile(
                  contentPadding: const EdgeInsets.all(AppTheme.space12),
                  leading: Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: AppTheme.brandBlue.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
                    ),
                    child: const Icon(Icons.image_outlined, color: AppTheme.brandBlue),
                  ),
                  title: Text(
                    item['title']?.toString() ?? 'Untitled',
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 16),
                  ),
                  subtitle: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: AppTheme.space4),
                      Text(
                        item['description']?.toString() ?? '',
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13),
                      ),
                      const SizedBox(height: AppTheme.space8),
                      Text(
                        formattedPrice,
                        style: const TextStyle(
                          color: AppTheme.brandBlue,
                          fontWeight: FontWeight.w700,
                          fontSize: 15,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, _) => Center(
          child: Text('Error loading feed: $error'),
        ),
      ),
    );
  }
}

class PlaceholderScreen extends StatelessWidget {
  final String title;
  final IconData icon;

  const PlaceholderScreen({super.key, required this.title, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 64, color: AppTheme.brandBlue.withOpacity(0.5)),
            const SizedBox(height: AppTheme.space16),
            Text(
              title,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: AppTheme.space8),
            const Text(
              'Phase implementation in progress',
              style: TextStyle(color: AppTheme.textSecondary),
            ),
          ],
        ),
      ),
    );
  }
}
