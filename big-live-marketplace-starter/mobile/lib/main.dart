import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

void main() => runApp(const MarketplaceApp());
class MarketplaceApp extends StatelessWidget {
  const MarketplaceApp({super.key});
  @override
  Widget build(BuildContext context) => MaterialApp(
    title: 'Big Live Marketplace',
    theme: ThemeData(colorSchemeSeed: const Color(0xFF2471C8), useMaterial3: true),
    home: const ListingFeed(),
  );
}
class ListingFeed extends StatefulWidget {
  const ListingFeed({super.key});
  @override
  State<ListingFeed> createState() => _ListingFeedState();
}
class _ListingFeedState extends State<ListingFeed> {
  late Future<List<dynamic>> listings;
  @override
  void initState() { super.initState(); listings = fetchListings(); }
  Future<List<dynamic>> fetchListings() async {
    const base = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000');
    final response = await http.get(Uri.parse('$base/api/v1/listings'));
    if (response.statusCode != 200) throw Exception('Could not load listings');
    return jsonDecode(response.body)['data'] as List<dynamic>;
  }
  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Big Live Marketplace')),
    body: FutureBuilder<List<dynamic>>(
      future: listings,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) return const Center(child: CircularProgressIndicator());
        if (snapshot.hasError) return Center(child: Text('Error: ${snapshot.error}'));
        final items = snapshot.data ?? [];
        if (items.isEmpty) return const Center(child: Text('No listings yet'));
        return ListView.builder(itemCount: items.length, itemBuilder: (context, i) {
          final item = items[i] as Map<String, dynamic>;
          return ListTile(title: Text(item['title']?.toString() ?? ''),
            subtitle: Text(item['description']?.toString() ?? '', maxLines: 2),
            trailing: Text('${item['currency'] ?? ''} ${((item['price_minor'] ?? 0) as num) / 100}'));
        });
      },
    ),
  );
}
