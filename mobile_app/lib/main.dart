import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Check if API session token exists
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('api_token');
  final bool isLoggedIn = token != null && token.isNotEmpty;

  runApp(SelaMobileApp(isLoggedIn: isLoggedIn));
}

class SelaMobileApp extends StatelessWidget {
  final bool isLoggedIn;

  const SelaMobileApp({Key? key, required this.isLoggedIn}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'صلة - إدارة المدارس والجمعيات',
      debugShowCheckedModeBanner: false,
      
      // Enforce Global RTL directionality at the very root of Sela
      builder: (context, child) {
        return Directionality(
          textDirection: TextDirection.rtl,
          child: child!,
        );
      },
      
      // Strict Arabic RTL Localization Configurations
      locale: const Locale('ar', 'AE'),
      supportedLocales: const [
        Locale('ar', 'AE'),
      ],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],

      // Sela Premium Modern Design System (Luxury Midnight & Gold)
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: const Color(0xff0f172a),
        scaffoldBackgroundColor: const Color(0xfff8fafc), // Premium soft white background
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xff0f172a),
          primary: const Color(0xff0f172a),
          secondary: const Color(0xffc5a021),
          surface: Colors.white,
          background: const Color(0xfff8fafc),
        ),
        
        // Cairo Font is the undisputed standard for luxury Arabic interfaces
        textTheme: GoogleFonts.cairoTextTheme(
          Theme.of(context).textTheme,
        ).apply(
          bodyColor: const Color(0xff334155), // Slate dark
          displayColor: const Color(0xff0f172a),
        ),
        
        // Modern Transparent AppBars (iOS Luxury Style)
        appBarTheme: AppBarTheme(
          backgroundColor: Colors.transparent,
          foregroundColor: const Color(0xff0f172a),
          elevation: 0,
          centerTitle: true,
          iconTheme: const IconThemeData(color: Color(0xff0f172a)),
          titleTextStyle: GoogleFonts.cairo(
            fontSize: 18,
            fontWeight: FontWeight.w900,
            color: const Color(0xff0f172a),
          ),
        ),

        // Premium Form Fields with soft filled backgrounds & gold focus indicators
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: const Color(0xfff8fafc),
          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide.none,
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: Color(0xffc5a021), width: 2),
          ),
          labelStyle: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 13),
          hintStyle: GoogleFonts.cairo(color: const Color(0xff94a3b8), fontSize: 13),
        ),
        
        // Luxury Flat Buttons
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xff0f172a),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            elevation: 2,
            shadowColor: const Color(0xff0f172a).withOpacity(0.15),
            textStyle: GoogleFonts.cairo(
              fontSize: 15,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ),
      
      home: isLoggedIn ? const DashboardScreen() : const LoginScreen(),
    );
  }
}
