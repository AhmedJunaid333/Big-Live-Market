import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';

class ListingDetailScreen extends StatelessWidget {
  final Map<String, dynamic> listing;

  const ListingDetailScreen({super.key, required this.listing});

  @override
  Widget build(BuildContext context) {
    final title = listing['title']?.toString() ?? 'Advertisement';
    final description = listing['description']?.toString() ?? 'No description provided.';
    final priceMinor = (listing['price_minor'] ?? 0) as num;
    final currency = listing['currency']?.toString() ?? 'PKR';
    final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
    final condition = listing['condition_code']?.toString() ?? 'used';
    final location = listing['location_label']?.toString() ?? 'Pakistan';
    final seller = listing['seller'] as Map<String, dynamic>?;
    final sellerName = seller?['name']?.toString() ?? 'Verified Member';
    final sellerPhone = seller?['phone_e164']?.toString() ?? '+92 300 1234567';

    return Scaffold(
      appBar: AppBar(
        title: Text(title, overflow: TextOverflow.ellipsis),
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined, color: AppTheme.champagne),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Listing link copied to clipboard!')),
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.favorite_border_rounded, color: AppTheme.champagne),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Added to your Saved Favorites!')),
              );
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Image Preview Container
            Container(
              height: 280,
              decoration: const BoxDecoration(
                color: AppTheme.obsidian,
              ),
              child: Stack(
                alignment: Alignment.center,
                children: [
                  Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 72,
                        height: 72,
                        decoration: BoxDecoration(
                          color: AppTheme.slateCard,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppTheme.borderGold),
                        ),
                        child: const Icon(Icons.photo_library_outlined, size: 36, color: AppTheme.champagne),
                      ),
                      const SizedBox(height: 12),
                      const Text(
                        'Verified HD Preview',
                        style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  Positioned(
                    bottom: 16,
                    left: 16,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: Colors.black.withValues(alpha: 0.7),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: AppTheme.borderGold),
                      ),
                      child: Text(
                        condition.toUpperCase(),
                        style: const TextStyle(color: AppTheme.champagne, fontSize: 11, fontWeight: FontWeight.w900),
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 16,
                    right: 16,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppTheme.successGreen.withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: AppTheme.successGreen),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.check_circle, size: 14, color: AppTheme.successGreen),
                          SizedBox(width: 4),
                          Text('ID Verified', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Price & Title Header
            Padding(
              padding: const EdgeInsets.all(AppTheme.space16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    formattedPrice,
                    style: const TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                      color: AppTheme.champagneDark,
                      letterSpacing: -0.5,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      const Icon(Icons.location_on_outlined, size: 16, color: AppTheme.textSecondary),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          location,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const Divider(height: 1),

            // Seller Information Card
            Padding(
              padding: const EdgeInsets.all(AppTheme.space16),
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
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 28,
                      backgroundColor: AppTheme.obsidian,
                      child: Text(
                        sellerName.isNotEmpty ? sellerName[0].toUpperCase() : 'S',
                        style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.champagne),
                      ),
                    ),
                    const SizedBox(width: AppTheme.space16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            sellerName,
                            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 2),
                          const Text(
                            'Member since 2026 · Response: 100%',
                            style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),

            // Description Section
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: AppTheme.space16),
              child: Container(
                padding: const EdgeInsets.all(AppTheme.space16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                  border: Border.all(color: AppTheme.borderSubtle),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Overview & Highlights',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.obsidian),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      description,
                      style: const TextStyle(fontSize: 14, color: AppTheme.textPrimary, height: 1.5),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 100),
          ],
        ),
      ),
      bottomSheet: Container(
        padding: const EdgeInsets.all(AppTheme.space16),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 20,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: Row(
          children: [
            Expanded(
              child: OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  minimumSize: const Size(0, 48),
                  side: const BorderSide(color: AppTheme.champagne, width: 1.5),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 8),
                ),
                icon: const Icon(Icons.phone, color: AppTheme.champagneDark, size: 20),
                label: const FittedBox(
                  fit: BoxFit.scaleDown,
                  child: Text(
                    'Call Seller',
                    style: TextStyle(color: AppTheme.obsidian, fontWeight: FontWeight.bold),
                  ),
                ),
                onPressed: () {
                  showDialog(
                    context: context,
                    builder: (ctx) => AlertDialog(
                      title: const Text('Direct Seller Contact'),
                      content: Text('Phone number: $sellerPhone'),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK')),
                      ],
                    ),
                  );
                },
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.champagne,
                  foregroundColor: AppTheme.obsidian,
                  minimumSize: const Size(0, 48),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 8),
                ),
                icon: const Icon(Icons.chat_bubble_outline, size: 20),
                label: const FittedBox(
                  fit: BoxFit.scaleDown,
                  child: Text(
                    'Chat / Offer',
                    style: TextStyle(fontWeight: FontWeight.w900),
                  ),
                ),
                onPressed: () {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Opening encrypted negotiation channel...')),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
